// src/validation/commentSchema.js
import * as yup from 'yup'

export const commentSchema = yup.object({
    content: yup
        .string()
        .required('コメント内容は必須です')
        .max(120, 'コメント内容は120文字以内で入力してください'),
})
