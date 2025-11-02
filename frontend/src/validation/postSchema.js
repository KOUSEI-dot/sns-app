// src/validation/postSchema.js
import * as yup from 'yup'

export const postSchema = yup.object({
    text: yup
        .string()
        .required('投稿内容は必須です')
        .max(120, '投稿内容は120文字以内で入力してください'),
})
