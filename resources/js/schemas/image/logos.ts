import { z } from 'zod';

const noSpecialChars = (name: string) => /^[a-zA-Z0-9-_ ]+$/.test(name);
const sizeLimit = 2 * 1024 * 1024; // 2MB
const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

const fileSchema = z.object({
    name: z.string().refine(n => noSpecialChars(n), {
        message: 'File name contains invalid characters'
    }),
    image: z.instanceof(File)
        .refine(f => f.size <= sizeLimit, {
            message: 'File size must be 2MB or less',
        })
        .refine(f => allowedMimeTypes.includes(f.type), {
            message: 'Invalid file type. Only images are allowed.',
        })
});

export default fileSchema;
