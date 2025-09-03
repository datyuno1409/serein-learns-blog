import { createRoot } from 'react-dom/client'
import { StrictMode } from 'react'
import App from './App.tsx'
import './index.css'

const renderApp = () => {
  const root = createRoot(document.getElementById("root")!);
  
  root.render(
    <StrictMode>
      <App />
    </StrictMode>
  );
  
  // Remove initial loader after app is rendered
  const loader = document.getElementById('initial-loader');
  if (loader) {
    loader.style.opacity = '0';
    loader.style.transition = 'opacity 0.5s ease';
    setTimeout(() => {
      loader.style.display = 'none';
    }, 500);
  }
};

// Render with a small delay to ensure DOM is ready
setTimeout(renderApp, 100);
