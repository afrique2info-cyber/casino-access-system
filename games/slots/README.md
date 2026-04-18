# 🎰 HTML5 Slot Machine Game

A professional, responsive HTML5 slot machine game built with CreateJS and JavaScript. Perfect for online casinos and gaming platforms.

![Slot Machine](https://img.shields.io/badge/Game-Slot_Machine-blue)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
![CreateJS](https://img.shields.io/badge/CreateJS-FF6F00?logo=adobe&logoColor=white)

## 🎮 Live Demo
[Play Slot Machine Game](https://your-username.github.io/slots-game/)

## 📋 Features
- **🎰 Professional Slot Machine** - 5-reel, multiple paylines
- **📱 Fully Responsive** - Works on desktop, tablet, and mobile
- **🎨 High-Quality Graphics** - Smooth animations and visual effects
- **🔊 Sound Effects** - Immersive audio with Howler.js
- **🔄 Touch & Mouse Support** - Optimized for all input methods
- **💰 Bet System** - Adjustable bet amounts
- **🏆 Paytable** - Clear winning combinations
- **🌐 Multi-language Ready** - Easy localization system

## 🚀 Quick Start

### Option 1: Direct Play
Simply open `index.html` in your browser:
```bash
open index.html
# or
xdg-open index.html
```

### Option 2: Local Server
```bash
# Using Python
python3 -m http.server 8000

# Using Node.js
npx serve .

# Using PHP
php -S localhost:8000
```

Then visit: http://localhost:8000

## 📁 Project Structure
```
slots-game/
├── index.html          # Main game file
├── css/               # Stylesheets
│   ├── main.css       # Main styles
│   ├── reset.css      # CSS reset
│   └── orientation_utils.css # Mobile orientation
├── js/                # JavaScript files
│   ├── CGame.js       # Main game logic
│   ├── CMain.js       # Game initialization
│   ├── CReelColumn.js # Reel mechanics
│   ├── CSlotSettings.js # Slot configuration
│   ├── settings.js    # Game settings
│   ├── createjs.min.js # CreateJS library
│   ├── howler.min.js  # Audio library
│   └── ...           # Other game modules
├── sounds/            # Audio files
├── sprites/           # Game graphics
└── favicon.ico        # Game icon
```

## 🛠️ Technologies Used
- **HTML5** - Game structure and canvas
- **JavaScript (ES5)** - Game logic and interactions
- **CreateJS** - Graphics and animations
- **Howler.js** - Audio management
- **jQuery** - DOM manipulation
- **CSS3** - Styling and responsive design

## 🎯 Game Features
- **5 Reels** - Classic slot machine layout
- **Multiple Paylines** - Various winning combinations
- **Adjustable Bets** - Set your wager amount
- **Auto-spin** - Continuous play option
- **Win Animations** - Visual feedback for wins
- **Balance Tracking** - Virtual currency system

## ⚙️ Configuration
Edit `js/settings.js` to customize:
- Starting balance
- Bet amounts
- Reel symbols
- Paytable values
- Audio settings
- Game speed

## 🌐 Deployment

### GitHub Pages (Free)
1. Push to GitHub repository
2. Go to Settings → Pages
3. Select branch (main/master)
4. Your game will be at: `https://username.github.io/repository`

### Netlify (Recommended)
```bash
# Install Netlify CLI
npm install -g netlify-cli

# Deploy
netlify deploy --prod
```

### Vercel
```bash
# Install Vercel CLI
npm i -g vercel

# Deploy
vercel --prod
```

## 🔧 Development

### Adding New Features
1. Modify slot logic in `js/CGame.js`
2. Update reel mechanics in `js/CReelColumn.js`
3. Add new symbols to sprite system
4. Test with local server

### Building for Production
The game is ready-to-use. No build process required!

## 📱 Mobile Optimization
- Touch-friendly spin button
- Responsive canvas sizing
- Orientation detection
- Fullscreen mode support
- Performance optimized for mobile

## 🎨 Customization

### Changing Theme
Edit `css/main.css`:
```css
:root {
  --slot-bg: #1a1a2e;
  --reel-bg: #2c3e50;
  --win-color: #f1c40f;
}
```

### Adding Languages
Edit `js/CLang.js`:
```javascript
var TEXT = {
  en: {
    SPIN: "Spin",
    BET: "Bet",
    WIN: "Win"
  },
  fr: {
    SPIN: "Tourner",
    BET: "Miser",
    WIN: "Gagner"
  }
};
```

## 🤝 Contributing
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments
- CreateJS team for amazing graphics library
- Howler.js for audio management
- All contributors and testers

## 📞 Support
For issues and questions:
- Open a GitHub Issue
- Check existing issues first

---

**Enjoy the game!** 🎰✨

*Remember to gamble responsibly. This is a simulation game for entertainment purposes.*
