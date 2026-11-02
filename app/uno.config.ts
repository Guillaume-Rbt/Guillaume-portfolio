import { theme } from '@unocss/preset-wind4';
import { defineConfig, presetWind4 } from 'unocss';

export default defineConfig({
    presets: [
        presetWind4(

        ),
    ],

    content: {
        filesystem: ['../../templates/**/*.twig', '../**/*.ts'],
        pipeline: {
            include: [/\.twig$/, /\.ts$/],
        },
    },
    outputToCssLayers: {
        allLayers: true,
    },
    layers: {
        'reset': -1,
        'utilities': 0,
        'default': 1,
    },

    theme: {
        spacing: {
            DEFAULT: 'calc(max(16px, 1.06vw) * .25)'
        },
        colors: {
            dark: '#00010e',
            "dark-blue": '#000411',
            "dark-blue-2": "#010716",
            "blue": "#0072d5",
            white: '#f4f9ff',
            "white-light": '#6e87a1',
            "border-color": "#061c31",
            "block-bg": "#000518",
            primary: "#0072d5",
            success: "#28a745",
            danger: "#dc3545",
            warning: "#ffc107"

        }
    },
    shortcuts: [
        ["btn", "cursor-pointer rounded-1 py-1.8 px-3 font-600"],
        [/^btn-(.+)$/,
            ([, color], { theme }) => {
                const themeColor = theme.colors?.[color];

                if (!themeColor || typeof themeColor !== "string") {
                    return "";
                }
                return `
                text-white
                bg-[${themeColor}]
                hover:bg-[color-mix(in_srgb,${themeColor}_85%,#000)]
                transition-colors
                duration-300
                `;
            },
        ],
        ["link", "cursor-pointer py-1 inline-flex flex-col flex-items-center relative after:opacity-0 hover:after:opacity-100 [&.selected]:after:opacity-100 after:content-[''] after:relative after:bottom-0 after:w-[80%] after:my-auto after:h-0.6  after:block  after:transition-all after:duration-300 [&.selected]:hover:after:w-full "],
        [/^lnk-(.+)$/, ([, color], { theme }) => {
            const themeColor = theme.colors?.[color];

            if (!themeColor || typeof themeColor !== "string") {
                return "";
            }
            return `after:bg-${color}`;
        }]


    ]


});