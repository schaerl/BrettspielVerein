<script setup lang="ts">
const { repository: eventRepository } = useEventRepository();
const itemsPerPage = 10;
const page = ref(1);
const refreshing = ref(false);

const { data } = useLazyAsyncData(() => eventRepository.getPagedEventData(page.value, itemsPerPage), { server: false });

const events = computed(() => data.value?.data ?? []);
const totalEvents = computed(() => data.value?.total ?? 0);

async function updatePage(newPage: number) {
  refreshing.value = true;
  page.value = newPage;
  await refreshNuxtData();
  refreshing.value = false;
}
</script>

<template>
  <UContainer>
    <Section title="Alle kommenden Events">
      <BVZSheet
        class="bg-secondary rounded-xl flex flex-col gap-4"
      >
        <EventCard
          v-for="eventData in events"
          :key="eventData.id"
          :data="eventData"
          class="bg-white rounded-xl"
        />
        <UPagination
          class="self-center"
          :disabled="refreshing"
          :items-per-page
          :page="page"
          :total="totalEvents"
          @update:page="updatePage"
        />
      </BVZSheet>
    </Section>
  </UContainer>
</template>

<style scoped>
</style>
