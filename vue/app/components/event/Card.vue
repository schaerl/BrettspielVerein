<script setup lang="ts">
const { data } = defineProps<{ data: EventData }>();
</script>

<template>
  <UContainer
    class="flex gap-4 flex-col-reverse sm:flex-row shadow-lg rounded-xl py-4"
    :class="{ priority: data.priority }"
  >
    <img
      src="/Logo1.png"
      class="max-w-[13rem] self-center"
    >
    <EventDetails :data />
  </UContainer>
</template>

<style scoped>
.priority {
  border: solid 1px var(--color-primary);
  box-shadow: 0 0 45px rgb(from var(--color-primary-600) r g b / 70%);
}
</style>
