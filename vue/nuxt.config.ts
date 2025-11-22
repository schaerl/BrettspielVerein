export default defineNuxtConfig({
  app: {
    head: {
      title: "Brettspielverein Zofingen",
      meta: [
        {
          name: "description",
          content: "Die Website des Brettspielverein Zofingens.",
        },
        {
          name: "og:description",
          content: "Die Website des Brettspielverein Zofingens.",
        },
        { name: "og:title", content: "Brettspielverein Zofingen" },
        { name: "og:type", content: "Website" },
        { name: "og:image", content: "./original_logo.png" },
      ],
      link: [
        { rel: "icon", type: "image/png", href: "./favicon.ico" },
        {
          rel: "apple-touch-icon",
          sizes: "180x180",
          href: "/apple-touch-icon.png",
        },
        {
          rel: "icon",
          type: "image/png",
          sizes: "32x32",
          href: "/favicon-32x32.png",
        },
        {
          rel: "icon",
          type: "image/png",
          sizes: "16x16",
          href: "/favicon-16x16.png",
        },
        { rel: "manifest", href: "/site.webmanifest" },
      ],
    },
  },
  compatibilityDate: "2024-11-01",
  css: ["~/assets/css/global.scss"],
  devServer: {
    port: 8000,
  },
  devtools: {
    enabled: true,
  },
  experimental: {
    payloadExtraction: false,
  },
  features: {
    inlineStyles: false,
  },
  fonts: {
    defaults: {
      weights: [400],
      styles: ["normal", "italic"],
      subsets: [
        "cyrillic-ext",
        "cyrillic",
        "greek-ext",
        "greek",
        "vietnamese",
        "latin-ext",
        "latin",
      ],
    },
    families: [{ name: "DM Sans" }],
  },
  imports: {
    dirs: ["types/*.ts"],
  },
  modules: ["@nuxt/ui", "@nuxt/fonts", "@vueuse/nuxt"],
  nitro: {
    prerender: {
      ignore: ["/200.html", "/404.html"],
    },
  },
  runtimeConfig: {
    public: {
      apiUrl: "",
    },
  },
  vite: {
    build: {
      cssCodeSplit: false
    }
  }
});
