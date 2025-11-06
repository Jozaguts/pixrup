// eslint.config.js
// NOTE: Flat config requires a standalone object ONLY for "ignores".

import { defineConfig } from 'eslint/config';
import prettier from 'eslint-config-prettier/flat';
import vue from 'eslint-plugin-vue';
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfig([
    {
        // ✅ Standalone ignore block (no other properties here)
        // In Flat Config, prefer ** globbing for nested patterns.
        ignores: [
            // Vendor/bundles/minified
            'node_modules/**',
            'vendor/**', // Laravel vendor
            'public/**', // public builds
            'resources/theme/**',
            'dist/**',
            'build/**',
            '**/*.min.js',
            // Opcional: ignora configuración si no quieres que ESLint la toque
            // 'tailwind.config.js',
            // 'vite.config.js',
        ],
    },

    // Vue + TS recommended (si tu proyecto lo usa)
    ...defineConfigWithVueTs(
        vue.configs['flat/essential'],
        vueTsConfigs.recommended,
    ),

    // Tus reglas del proyecto
    {
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
            // Opcional: relaja expresiones sueltas si usas libs que hacen IIFE sin asignar
            // '@typescript-eslint/no-unused-expressions': ['error', { allowShortCircuit: true, allowTernary: true, allowTaggedTemplates: true }],
        },
    },

    // Desactiva reglas que chocan con Prettier (formato)
    prettier,
]);
