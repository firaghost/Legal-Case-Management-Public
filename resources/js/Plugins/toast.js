import { createApp, h } from 'vue';
import { useToast } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Toast component
const ToastComponent = {
  name: 'Toast',
  props: {
    message: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
    },
    duration: {
      type: Number,
      default: 5000,
    },
  },
  setup(props) {
    const toast = useToast();
    
    // Show the toast when the component is mounted
    const showToast = () => {
      const options = {
        position: 'top-right',
        timeout: props.duration,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: true,
        hideProgressBar: false,
        closeButton: 'button',
        icon: true,
        rtl: false,
      };
      
      switch (props.type) {
        case 'success':
          toast.success(props.message, options);
          break;
        case 'error':
          toast.error(props.message, options);
          break;
        case 'warning':
          toast.warning(props.message, options);
          break;
        case 'info':
        default:
          toast.info(props.message, options);
          break;
      }
    };
    
    // Show the toast immediately
    showToast();
    
    return () => null; // Render nothing
  },
};

// Toast container component
const ToastContainer = {
  name: 'ToastContainer',
  setup() {
    // This component is just a container for the toast portal
    return () => h('div', { id: 'toast-container' });
  },
};

// Install function for Vue.use()
const install = (app) => {
  // Create a root container for the toast component
  const toastContainer = document.createElement('div');
  toastContainer.id = 'toast-root';
  document.body.appendChild(toastContainer);
  
  // Mount the toast container
  const toastApp = createApp(ToastContainer);
  const toast = useToast();
  
  // Make toast available globally
  app.config.globalProperties.$toast = toast;
  
  // Mount the toast container
  toastApp.mount('#toast-root');
  
  // Add toast to the app instance
  app.provide('toast', toast);
  
  // Add toast to the global window object for easy access
  window.toast = toast;
};

// Create a toast instance
const createToast = (message, options = {}) => {
  const defaultOptions = {
    type: 'info',
    duration: 5000,
  };
  
  const mergedOptions = { ...defaultOptions, ...options };
  
  // Create a temporary container for the toast
  const container = document.createElement('div');
  document.body.appendChild(container);
  
  // Create a new Vue app for the toast
  const toastApp = createApp({
    render() {
      return h(ToastComponent, {
        message,
        type: mergedOptions.type,
        duration: mergedOptions.duration,
        onClose: () => {
          // Clean up after the toast is closed
          toastApp.unmount();
          document.body.removeChild(container);
        },
      });
    },
  });
  
  // Mount the toast
  toastApp.mount(container);
  
  // Return a function to close the toast
  return () => {
    toastApp.unmount();
    if (document.body.contains(container)) {
      document.body.removeChild(container);
    }
  };
};

// Create individual toast methods
const toast = {
  // Show a toast with the given message and options
  show(message, options = {}) {
    return createToast(message, options);
  },
  
  // Success toast
  success(message, options = {}) {
    return createToast(message, { ...options, type: 'success' });
  },
  
  // Error toast
  error(message, options = {}) {
    return createToast(message, { ...options, type: 'error' });
  },
  
  // Warning toast
  warning(message, options = {}) {
    return createToast(message, { ...options, type: 'warning' });
  },
  
  // Info toast
  info(message, options = {}) {
    return createToast(message, { ...options, type: 'info' });
  },
};

export { toast, install };
export default toast;
