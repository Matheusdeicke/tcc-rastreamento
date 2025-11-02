// tailwind.config.js
export default {
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          900: "#000814", // mais escuro
          800: "#001d3d",
          700: "#003566",
          accent: "#ffc300",
          accentLight: "#ffd60a",
        },
      },
      boxShadow: {
        soft: "0 10px 30px rgba(0,0,0,0.25)",
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
};
