import { defineConfig, presetWind4 } from 'unocss';

export default defineConfig({
    presets: [
        presetWind4(
            
        ),
    ],
               
    content: {
        filesystem: ['../../templates/**/*.twig'],
        pipeline: {
            include: [/\.twig$/],
        },
    },
outputToCssLayers: {
  allLayers: true,
},
    layers: {
  'reset': -1,
  'utilities': 0,
  'default': -2,
},

    theme: {
        spacing: {
            DEFAULT: 'calc(max(16px, 1.06vw) * .25)'
        },
        colors:  {
            dark: '#00010e',
            "dark-blue": '#000411',
            "dark-blues-2": "#010716",
            "blue": "#0072d5",
            "blue-2": "#0067c2",
            white: '#f4f9ff', 
            "white-light": '#6e87a1',
            "border-color": "#061c31",
            "block-bg": "#000518"
           
        }
    }


});