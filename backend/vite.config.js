import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
<<<<<<< HEAD
=======
import tailwindcss from '@tailwindcss/vite';
>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
<<<<<<< HEAD
    ],
});
=======
        tailwindcss(),
    ],
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },

>>>>>>> 5d48079fd2491b555b2bd37ceedda945168a6850
