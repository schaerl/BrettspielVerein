<script setup lang="ts">
const props = defineProps<{ data: EventData }>();
const formattedPrice = computed(() =>
  Number.isFinite(props.data.price) ? `${props.data.price}.-` : "-"
);
</script>

<template>
  <UContainer>
    <table>
      <tbody>
        <tr>
          <td>Ort:</td>
          <td>{{ data.location }}</td>
        </tr>
        <tr>
          <td>Zeit:</td>
          <td>{{ data.start_time }}</td>
        </tr>
        <tr>
          <td>Eintritt:</td>
          <td>{{ formattedPrice }}</td>
        </tr>
      </tbody>
    </table>
  </UContainer>
</template>

<style scoped>
tr td:nth-child(1) {
  font-weight: bold;
}

tr td:nth-child(2) {
  padding-left: 12px;
}
</style>
