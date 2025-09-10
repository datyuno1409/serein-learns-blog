// Typewriter Effect Class
class TypewriterEffect {
    constructor(element, text, speed = 100) {
        this.element = element;
        this.text = text;
        this.speed = speed;
        this.currentIndex = 0;
        this.isTyping = false;
    }

    type() {
        if (this.currentIndex < this.text.length) {
            this.element.textContent = this.text.substring(0, this.currentIndex + 1);
            this.currentIndex++;
            setTimeout(() => this.type(), this.speed);
        } else {
            this.isTyping = false;
            // Add blinking cursor effect
            this.element.style.borderRight = '2px solid rgba(0, 255, 255, 0.8)';
            setInterval(() => {
                this.element.style.borderRight = this.element.style.borderRight === 'none' 
                    ? '2px solid rgba(0, 255, 255, 0.8)' 
                    : 'none';
            }, 500);
        }
    }

    start() {
        if (!this.isTyping) {
            this.isTyping = true;
            this.element.textContent = '';
            this.currentIndex = 0;
            this.type();
        }
    }
}

// Matrix Rain Variables
let matrixCanvas, matrixCtx, matrixInterval;
const matrixChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789@#$%^&*()";
let matrixColumns = [];

// Initialize Matrix Rain Effect
function initMatrixRain() {
    try {
        matrixCanvas = document.createElement('canvas');
        matrixCanvas.style.position = 'fixed';
        matrixCanvas.style.top = '0';
        matrixCanvas.style.left = '0';
        matrixCanvas.style.width = '100%';
        matrixCanvas.style.height = '100%';
        matrixCanvas.style.pointerEvents = 'none';
        matrixCanvas.style.zIndex = '-1';
        matrixCanvas.style.opacity = '0.1';
        document.body.appendChild(matrixCanvas);
        
        matrixCtx = matrixCanvas.getContext('2d');
        resizeCanvas();
        
        window.addEventListener('resize', resizeCanvas);
        
        matrixInterval = setInterval(drawMatrix, 100);
    } catch (error) {
        console.log('Matrix rain effect could not be initialized:', error);
    }
}

// Resize Canvas Function
function resizeCanvas() {
    if (!matrixCanvas) return;
    
    matrixCanvas.width = window.innerWidth;
    matrixCanvas.height = window.innerHeight;
    
    const columns = Math.floor(matrixCanvas.width / 20);
    matrixColumns = [];
    for (let i = 0; i < columns; i++) {
        matrixColumns[i] = 1;
    }
}

// Draw Matrix Function
function drawMatrix() {
    if (!matrixCtx || !matrixCanvas) return;
    
    matrixCtx.fillStyle = 'rgba(0, 0, 0, 0.05)';
    matrixCtx.fillRect(0, 0, matrixCanvas.width, matrixCanvas.height);
    
    matrixCtx.fillStyle = '#0f0';
    matrixCtx.font = '15px monospace';
    
    for (let i = 0; i < matrixColumns.length; i++) {
        const text = matrixChars.charAt(Math.floor(Math.random() * matrixChars.length));
        matrixCtx.fillText(text, i * 20, matrixColumns[i] * 20);
        
        if (matrixColumns[i] * 20 > matrixCanvas.height && Math.random() > 0.975) {
            matrixColumns[i] = 0;
        }
        matrixColumns[i]++;
    }
}

// Initialize Typewriter Effect
function initTypewriter() {
    const titleElement = document.querySelector('#typewriter-title');
    if (titleElement) {
        const originalText = 'Learning with Serein';
        const typewriter = new TypewriterEffect(titleElement, originalText, 150);
        
        setTimeout(() => {
            typewriter.start();
        }, 1000);
    }
}

// Initialize Scroll Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0) scale(1)';
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.article-card, .project-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px) scale(0.95)';
        card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(card);
    });
}

// Initialize Cursor Effect
function initCursorEffect() {
    document.addEventListener('mousemove', function(e) {
        let cursor = document.querySelector('.cyber-cursor');
        if (!cursor) {
            cursor = document.createElement('div');
            cursor.className = 'cyber-cursor';
            cursor.style.cssText = `
                position: fixed;
                width: 20px;
                height: 20px;
                border: 2px solid rgba(0, 255, 255, 0.5);
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                transition: all 0.1s ease;
                box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
            `;
            document.body.appendChild(cursor);
        }
        
        cursor.style.left = (e.clientX - 10) + 'px';
        cursor.style.top = (e.clientY - 10) + 'px';
    });

    // Add glow effect to interactive elements
    document.addEventListener('DOMContentLoaded', function() {
        const interactiveElements = document.querySelectorAll('a, button, .article-card, .project-card');
        
        interactiveElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                const cursor = document.querySelector('.cyber-cursor');
                if (cursor) {
                    cursor.style.transform = 'scale(1.5)';
                    cursor.style.borderColor = 'rgba(138, 43, 226, 0.8)';
                    cursor.style.boxShadow = '0 0 30px rgba(138, 43, 226, 0.5)';
                }
            });
            
            element.addEventListener('mouseleave', function() {
                const cursor = document.querySelector('.cyber-cursor');
                if (cursor) {
                    cursor.style.transform = 'scale(1)';
                    cursor.style.borderColor = 'rgba(0, 255, 255, 0.5)';
                    cursor.style.boxShadow = '0 0 20px rgba(0, 255, 255, 0.3)';
                }
            });
        });
    });
}

// Initialize all effects when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initMatrixRain();
    initTypewriter();
    initScrollAnimations();
    initCursorEffect();
});