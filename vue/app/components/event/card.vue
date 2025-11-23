<script setup lang="ts">
const props = defineProps<{ data: EventData }>();
</script>

<template>
<UPageCard class="rounded-xl"
           title="data.name">
    <div class="d-flex flex-wrap">
      <UBadge
        color="footer"
        variant="flat"
        class="ma-4"
        size="x-large"
        style="width: 100px"
      >
        {{ data.date }}
      </UBadge>
      <UAvatar class="ma-3" rounded="0" size="250" src="/Logo1.png" />
      <div>
        <EventDetails :data> </EventDetails>
      </div>
    </div>
  </UPageCard>
</template>
