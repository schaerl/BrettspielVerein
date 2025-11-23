<script setup lang="ts">
const { alertData } = defineProps<{
  alertData: AlertStatus[];
}>();

const visibility = computed(() => alertData.length !== 0);
</script>

<template>
  <UContainer
    v-show="visibility"
    class="position-fixed"
    style="top: 60px; left: 50%; transform: translate(-50%, 0%); z-index: 20"
  >
    <UAlert
      v-for="alert in alertData"
      :text="alert.message"
      :type="alert.type"
      class="my-4"
    ></UAlert>
  </UContainer>
</template>

<style scoped></style>
