<?php

namespace App\Tests\Http\Admin\api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesApiTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $root;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->root = static::getContainer()->getParameter('uploads');
        (new Filesystem())->remove($this->root);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->root);
        parent::tearDown();
    }

    public function testFolderLifecycle(): void
    {
        $this->json('POST', '/api/folders', ['name' => 'documents']);
        $this->assertResponseStatusCodeSame(201);
        $this->assertSame(['id' => 'documents', 'name' => 'documents', 'parent' => null], $this->body());

        $this->json('POST', '/api/folders', ['name' => 'factures', 'parent' => 'documents']);
        $this->assertSame(['id' => 'documents/factures', 'name' => 'factures', 'parent' => 'documents'], $this->body());

        $this->json('POST', '/api/folders', ['name' => 'factures', 'parent' => 'documents']);
        $this->assertResponseStatusCodeSame(409);
        $this->assertSame('Un dossier nomme "factures" existe deja ici.', $this->body()['message']);

        $this->json('PATCH', '/api/folders', ['id' => 'documents', 'name' => 'docs']);
        $this->assertResponseIsSuccessful();
        $this->assertSame('docs', $this->body()['id']);

        $this->json('GET', '/api/folders');
        $this->assertSame(['docs', 'docs/factures'], array_column($this->body(), 'id'));

        $this->json('DELETE', '/api/folders', ['id' => 'docs']);
        $this->assertResponseIsSuccessful();
        $this->json('GET', '/api/folders');
        $this->assertSame([], $this->body());

        $this->json('PATCH', '/api/folders', ['id' => 'nope', 'name' => 'x']);
        $this->assertResponseStatusCodeSame(404);
        $this->assertSame('Dossier introuvable.', $this->body()['message']);
    }

    public function testUploadListMoveRenameDelete(): void
    {
        $this->json('POST', '/api/folders', ['name' => 'archives']);

        $this->upload(['a' => $this->tmpFile('rapport.txt', 'hello'), 'b' => $this->tmpFile('notes.txt', 'x')], 'archives');
        $this->assertResponseStatusCodeSame(201);
        $files = $this->body()['files'];
        $this->assertSame('archives/rapport.txt', $files[0]['id']);
        $this->assertSame('txt', $files[0]['extension']);
        $this->assertSame('text/plain', $files[0]['type']);
        $this->assertSame('archives', $files[0]['parent']);
        $this->assertFileExists($this->root.'/archives/notes.txt');

        $this->client->request('GET', '/api/files', ['parent' => 'archives']);
        $this->assertCount(2, $this->body());

        $this->json('POST', '/api/files/move', ['id' => 'archives/rapport.txt']);
        $this->assertResponseIsSuccessful();
        $this->assertSame(['id' => 'rapport.txt', 'name' => 'rapport.txt', 'parent' => null, 'extension' => 'txt', 'type' => 'text/plain'], $this->body());

        $this->json('PATCH', '/api/files', ['id' => 'rapport.txt', 'name' => 'final.txt']);
        $this->assertSame('final.txt', $this->body()['id']);

        $this->json('DELETE', '/api/files', ['id' => 'final.txt']);
        $this->assertSame(['file' => 'final.txt'], $this->body());
        $this->assertFileDoesNotExist($this->root.'/final.txt');
    }

    public function testUploadNeverOverwritesAndIsAtomic(): void
    {
        $this->upload(['f' => $this->tmpFile('a.txt', '1')]);
        $this->assertResponseStatusCodeSame(201);

        $this->upload(['f' => [$this->tmpFile('new.txt', '2'), $this->tmpFile('a.txt', '3')]]);
        $this->assertResponseStatusCodeSame(409);
        $this->assertSame('1', file_get_contents($this->root.'/a.txt'));
        $this->assertFileDoesNotExist($this->root.'/new.txt');
    }

    public function testPathTraversalIsRejected(): void
    {
        $this->client->request('GET', '/api/files', ['parent' => '../']);
        $this->assertResponseStatusCodeSame(400);

        $this->json('POST', '/api/folders', ['name' => '..']);
        $this->assertResponseStatusCodeSame(400);

        $this->upload(['f' => $this->tmpFile('x.txt', '1')]);
        $this->json('POST', '/api/files/move', ['id' => 'x.txt', 'parent' => '../../etc']);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testInvalidPayloadReturnsJsonMessage(): void
    {
        $this->json('POST', '/api/folders', ['name' => '']);
        $this->assertResponseStatusCodeSame(422);
        $this->assertArrayHasKey('message', $this->body());
    }

    private function json(string $method, string $uri, array $data = []): void
    {
        $this->client->request($method, $uri, server: ['CONTENT_TYPE' => 'application/json'], content: json_encode($data));
    }

    private function upload(array $files, ?string $parent = null): void
    {
        $this->client->request('POST', '/api/files', $parent ? ['parent' => $parent] : [], $files);
    }

    private function tmpFile(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upl');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }

    private function body(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
    }
}
