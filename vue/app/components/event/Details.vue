<script setup lang="ts">
const { data } = defineProps<{ data: EventData }>();
const formattedPrice = computed(() => {
  return !isNaN(parseFloat(data.price)) ? `${data.price}.-` : "-";
});

function isLink(text: string) {
  return text.startsWith("http");
}
</script>

<template>
  <div>
    <div class="flex flex-wrap gap-2 mb-4">
      <UBadge
        :label="data.date"
        class="rounded-full min-w-[8rem] text-center"
        size="xl"
        color="footer"
        :ui="{
          base: 'justify-center',
        }"
      />
      <h2>{{ data.name }}</h2>
    </div>
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
        <template v-if="data.extra">
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>Extra Informationen:</td>
            <td v-if="isLink(data.extra)">
              <a
                class="hover-link"
                :href="data.extra"
              >
                {{ data.extra }}
              </a>
            </td>
            <td v-else>
              {{ data.extra }}
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
tr td:nth-child(1) {
  font-weight: bold;
}

tr td:nth-child(2) {
  padding-left: 12px;
}

td {
  vertical-align: top;
}
</style>
