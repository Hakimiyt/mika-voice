import express from 'express';
import dotenv from 'dotenv';
import OpenAI from 'openai';

dotenv.config();

const app = express();
app.use(express.json());

// Sambung ke Groq menggunakan SDK OpenAI
const groq = new OpenAI({
  apiKey: process.env.GROQ_API_KEY,
  baseURL: 'https://api.groq.com/openai/v1',
});

// Endpoint untuk pemprosesan chat
app.post('/api/chat', async (req, res) => {
  try {
    const { messages } = req.body;
    const provider = process.env.BRAIN_PROVIDER;

    if (provider === 'groq') {
      const completion = await groq.chat.completions.create({
        model: process.env.GROQ_MODEL || 'openai/gpt-oss-120b',
        messages: [
          {
            role: 'system',
            content: 'Nama anda ialah Mika, seorang pembantu maya 3D yang peramah, comel, dan rajin membantu. Jawab secara ringkas dan mesra dalam bahasa Melayu.'
          },
          ...messages
        ],
      });

      const reply = completion.choices[0]?.message?.content || 'Maaf, saya tidak dapat memproses permintaan anda.';
      return res.json({ text: reply });
    }

    // Sambungan logik pembekal lain jika ada (ollama / anthropic)

  } catch (error) {
    console.error('Chat API Error:', error);
    res.status(500).json({ error: error.message });
  }
});