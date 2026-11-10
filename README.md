# Mika Voice

An AI chatbot with a 3D (VRM) avatar that can **talk, move its mouth, blink, and follow your cursor**. Mika understands Malay, English and Japanese, and replies in the same language as your message.

## Features

- 3D avatar in `.vrm` format (Three.js + `@pixiv/three-vrm`)
- AI brain powered by **Groq** (model `openai/gpt-oss-120b`)
- Automatically replies in the same language (BM / EN / JP)
- Mika's voice uses the browser's built-in voices (`speechSynthesis`), free and with no extra installation
- Voice input via microphone: click 🎙️ to start, speak for as long as you like, click ⏹ to send
- Facial expressions (neutral, happy, think, surprised) and mouth movement while speaking
- The API key stays on the server (`.env`) and never reaches the browser

## How It Works

```
Type / 🎙️ microphone  →  public/api/chat.php  →  Groq API
                                    ↓
                         reply + language code
                                    ↓
              speechSynthesis (browser voice) + avatar mouth movement
```

## Requirements

- [Laragon](https://laragon.org) (Apache + PHP 8+) or any local PHP server
- A free Groq account for an API key: https://console.groq.com
- **Google Chrome** or **Microsoft Edge** (for the microphone and voices)
- An internet connection (Three.js is loaded from a CDN)
- Your own `.vrm` model file (see the [Avatar Model](#avatar-model) section)

## Installation

1. Copy the project folder to `C:\laragon\www\mika-voice`.
2. Copy `.env.example` to `.env`, then fill in `GROQ_API_KEY`:

   ```
   GROQ_API_KEY=put_your_key_here
   GROQ_MODEL=openai/gpt-oss-120b
   ```

3. Place your VRM model at `public/models/mika.vrm`.
4. Start Laragon (Apache + PHP).
5. Open **http://localhost/mika-voice/public/** in Chrome or Edge.

> Use `localhost`. The microphone only works on `localhost` or HTTPS. Don't open `index.html` directly (`file://`), because the API won't work.

## Usage

- **Typing:** write a message, then press Enter or the ➤ button.
- **Voice:** click 🎙️, start speaking, and click ⏹ when you're done. The speech recognition language follows the **BM / EN / JP** dropdown next to the input box.
- **Expressions:** click the emoji icon in the top-right corner to temporarily change Mika's expression.
- Mika replies in the language you use, regardless of what is selected in the dropdown.

## Avatar Model

This project **does not include the model file** due to licensing issues.

- Make your own with [VRoid Studio](https://vroid.com/en/studio) (free) and export it as `.vrm`, or use a model whose license permits your use.
- Place the file at `public/models/mika.vrm`.
- Check the model's license terms before publishing the project publicly or commercially.
- Don't use models of copyrighted characters.

## `.env` Settings

| Variable | Description | Default |
|---|---|---|
| `GROQ_API_KEY` | Groq API key | (required) |
| `GROQ_MODEL` | Groq model to use | `openai/gpt-oss-120b` |

## Troubleshooting

**`Groq API Reject ... model does not exist`**
The model you're using has been deprecated by Groq. Check the current model list at https://console.groq.com/docs/models and update `GROQ_MODEL`.

**`AI did not provide an answer`**
Reasoning models can return empty `content`. Make sure `chat.php` sends `include_reasoning: false` and a sufficient `max_completion_tokens`.

**Mika doesn't speak / the voice sounds odd**
The voice comes from the browser. Use **Microsoft Edge** for a natural-sounding Malay voice (Yasmin). Check the available voices in the Console (F12):

```js
speechSynthesis.getVoices().map(v => v.name + " | " + v.lang)
```

**The microphone doesn't work**
Allow microphone access (the lock icon next to the URL), and make sure you're using Chrome or Edge on `localhost`.

**The avatar doesn't appear**
Check that the file `public/models/mika.vrm` exists, and open the Console (F12) to look for a `VRM LOAD ERROR`.

## Security

- Don't commit the `.env` file. It is already ignored by `.gitignore`.
- `.htaccess` blocks direct access to `.env` on Apache.
- If you deploy to a public server, add **rate limiting or authentication** to `public/api/chat.php` so your API quota isn't abused.
- `chat.php` disables SSL certificate verification (`verify_peer => false`) to make it easy to run on Laragon. **Do not** use this setting on a real server.

## Tech Stack

- PHP (simple backend)
- [Three.js](https://threejs.org) and [@pixiv/three-vrm](https://github.com/pixiv/three-vrm)
- [Groq](https://groq.com) API
- Web Speech API (`speechSynthesis` and `SpeechRecognition`)

## License

Add a `LICENSE` file (e.g. MIT) according to your preference.
