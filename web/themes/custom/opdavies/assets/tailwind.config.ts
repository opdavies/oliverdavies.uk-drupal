import colours from "tailwindcss/colors";
import formsPlugin from '@tailwindcss/forms';
import type { Config } from 'tailwindcss'
import typographyPlugin from "@tailwindcss/typography";

export default {
  content: [
    "./opdavies.theme",
    "./templates/**/*.html.twig",
  ],
  theme: {
    colors: {
      black: "#000",
      blue: {
        primary: "#24608A",
        400: "#60a5fa",
      },
      current: "currentColor",
      gray: colours.stone,
      grey: colours.stone,
      inherit: "inherit",
      transparent: "transparent",
      white: "#fff",
    },

    extend: {
      fontFamily: {
        sans: [
          "Roboto Condensed",
          "Arial",
          "Helvetica Neue",
          "Helvetica",
          "sans-serif",
        ],
      },
    },
  },
  plugins: [formsPlugin, typographyPlugin],
} satisfies Config

