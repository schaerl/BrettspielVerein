<script setup lang="ts">
const { size = 50, onPrimary = false } = defineProps<{
  size?: number;
  onPrimary?: boolean;
}>();
//const theme = useTheme();
const calculatedColor = computed(() => {
  return onPrimary ? "white" /*theme.current.value.colors.onPrimary*/ : "black";
});
</script>
<template>
  <NuxtLink to="/">
<!--    <VRow class="d-inline-flex align-center grow-link">
      <VCol
        ><img
          :class="{ invert: onPrimary }"
          :style="`width: ${size}px; display: inline;`"
          src="/logoNoBG.png"
      /></VCol>
      <VCol>
        <span class="navbar-brand-text">
          Brettspielverein
          <small>Zofingen</small>
        </span>
      </VCol>
</VRow> -->
  </NuxtLink>
</template>

<style scoped>
.grow-link {
  transition: all 0.07s ease-in-out;

  &:hover {
    transform: scale(1.1);
  }
}

a {
  &,
  &.visited,
  &:hover,
  &:active,
  &:link {
    color: v-bind(calculatedColor);
  }
}

.navbar-brand-text {
  line-height: normal;
  position: relative;
  bottom: 5px;
  font-weight: bold;
}

.navbar-brand-text small {
  display: block;
  font-size: 10px;
  line-height: 1;
  text-transform: uppercase;
}

.invert {
  filter: brightness(0) invert(1);
}
</style>
