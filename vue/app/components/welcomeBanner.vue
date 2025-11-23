<script setup lang="ts">
const activeIndex = ref(0);

function updateActiveIndex(event: any) {
  activeIndex.value = event;
}

// Used to revert the direction of the transition, to have uni-directional scrolling
const isFirstElementActive = computed(() => {
  return activeIndex.value == 0;
});
</script>

<template>
  <div>
    <h1>Willkommen beim Brettspielverein Zofingen</h1>
    <h2>
      <span>Brettspiele</span>
     <!-- <v-carousel
        cycle
        continuous
        :direction="'vertical'"
        hide-delimiter-background
        hide-delimiters
        :interval="4500"
        :show-arrows="false"
        :height="31"
        :reverse="isFirstElementActive"
        @update:model-value="updateActiveIndex"
      >
        <div class="title-color-wrapper">
          <v-carousel-item>machen Spass</v-carousel-item>
          <v-carousel-item>verbinden uns</v-carousel-item>
        </div>
     </v-carousel> -->
    </h2>
  </div>
</template>
<style scoped>
.title-color-wrapper {
  color: rgb(var(--v-theme-primary-darken-1));
}
</style>
