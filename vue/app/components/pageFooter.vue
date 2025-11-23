<script setup>
const theme = useTheme();
const { xs } = useDisplay();
</script>

<template>
  <UContainer class="footer-content">
   <!-- <VRow>
      <VCol :cols="xs ? 8 : ''">
        <VContainer>
          <VRow>
            <LogoTextWrapper></LogoTextWrapper>
          </VRow>
          <VRow class="mt-4">
            <NuxtLink class="hover-link" to="/privacy"
              >Datenschutzerklärung</NuxtLink
            >
          </VRow>
        </VContainer>
      </VCol>
      <VCol :cols="xs ? 4 : ''">
        <VContainer>
          <VRow class="justify-end">
            <VBtn
              class="ma-2"
              color="header"
              elevation="0"
              icon="mdi-instagram"
              href="https://www.instagram.com/BrettspielZofingen"
              target="_blank"
            ></VBtn>
          </VRow>
        </VContainer>
      </VCol>
   </VRow> -->
  </UContainer>

  <WaveVariant1
    lower
    :color="theme.current.value.colors.footer"
    class="w-100"
    style="position: absolute; bottom: 0px"
  ></WaveVariant1>
</template>

<style scoped>
a {
  text-decoration: none;
  :link,
  :visited {
    color: inherit;
  }
}

.footer-content {
  z-index: 1;
}
</style>
