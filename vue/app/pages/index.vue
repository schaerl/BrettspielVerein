<script setup lang="ts">
const emit = defineEmits<{
  provideNavigation: [navInfo: HeaderSpec[]];
}>();

const { repository: eventRepository } = useEventRepository();
const { data } = useLazyAsyncData(() => eventRepository.getEventData(), { server: false });

const { alertData, triggerAlert } = useAlert();

type AvailableHeaders = "Über uns" | "Mitgliedschaft" | "Events" | "Kontakt";
const headerMap: Map<AvailableHeaders, HeaderSpec> = reactive(new Map());
function handleNavigation(event: HeaderSpec) {
  headerMap.set(event.displayName as AvailableHeaders, event);
}

const home = useTemplateRef("home");
onMounted(() => {
  const navigationItems = [
    { displayName: "Home", scrolledBeginningToTop: ref(true), goTo: () => home.value!.scrollIntoView({ behavior: "smooth" }) },
    headerMap.get("Über uns")!,
    headerMap.get("Mitgliedschaft")!,
    headerMap.get("Events")!,
    headerMap.get("Kontakt")!,
  ];
  emit("provideNavigation", navigationItems);
});
</script>

<template>
  <div ref="home">
    <AlertContainer :alert-data="alertData" />
    <div>
      <ImageContainer :variant="1">
        <WelcomeBanner />
      </ImageContainer>
      <NavScrollWrapper
        title="Über uns"
        @provide-navigation="handleNavigation"
      >
        <Section title="Über uns">
          <div>
            Wir treffen uns 1x im Monat jeweils an einem Freitagabend im Spittelhof Zofingen und spielen Brettspiele jeder Art. Von gehobenen Familienspielen, bis mehrstündigen Expertenspielen ist für Jeden etwas dabei! Spielerfahrung ist von Vorteil, aber absolut nicht erforderlich. Die Teilnahme an unserem Spieleabend steht allen Altersgruppen offen. Bei uns werden einige Spiele zur Verfügung gestellt und erklärt. Es dürfen aber sehr gerne auch eigene Spiele mitgebracht werden! Perfekt also für alle, die zu Hause wegen fehlenden Mitspielern noch ungespielte Spiele rumliegen haben, die schon lange wieder mal ein bestimmtes Spiel spielen wollten und natürlich auch für alle anderen, die einfach gerne Spiele spielen.
          </div>
        </Section>
      </NavScrollWrapper>
      <ImageContainer :variant="2">
        <div>
          <h2>Abonniere unseren Newsletter</h2>
          <div class="pb-4">
            Sei stets informiert über die nächsten Events!
          </div>
          <NewsletterForm @on-submission="(event: AlertStatus) => triggerAlert(event)" />
        </div>
        <template #right>
          <WhatsappCode />
        </template>
      </ImageContainer>
      <NavScrollWrapper
        title="Mitgliedschaft"
        @provide-navigation="handleNavigation"
      >
        <Section title="Vereinsmitgliedschaft">
          <div class="dual-slot-container">
            <div class="dual-container-item">
              <MembershipTable />
            </div>
            <BVZSheet class="dual-container-item bg-header rounded-xl">
              <h2 class="text-white mb-4">
                Anmeldung Mitgliedschaft
              </h2>
              <SignUpForm @on-submission="(event: AlertStatus) => triggerAlert(event)" />
            </BVZSheet>
          </div>
        </Section>
      </NavScrollWrapper>
      <NavScrollWrapper
        title="Events"
        @provide-navigation="handleNavigation"
      >
        <Section title="Die nächsten Veranstaltungen">
          <BVZSheet
            class="bg-secondary rounded-xl flex flex-col gap-4"
          >
            <EventCard
              v-for="eventData in data"
              :key="eventData.id"
              :data="eventData"
              class="bg-white rounded-xl"
            />
            <UButton
              class="w-fit rounded-full self-end"
              size="xl"
              to="/events"
            >
              Alle Events
            </UButton>
          </BVZSheet>
        </Section>
      </NavScrollWrapper>
      <NavScrollWrapper
        title="Kontakt"
        @provide-navigation="handleNavigation"
      >
        <Section title="Kontakt">
          <div class="dual-slot-container">
            <div class="dual-container-item">
              <h2>Fragen?</h2>
              <BVZSheet
                class="bg-secondary rounded-xl"
              >
                <QuestionForm @on-submission="(event: AlertStatus) => triggerAlert(event)" />
              </BVZSheet>
            </div>
            <div class="dual-container-item">
              <h2>Wo spielen wir?</h2>
              <MapWrapper />
            </div>
          </div>
        </Section>
      </NavScrollWrapper>
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.dual-slot-container {
  @apply flex flex-col md:flex-row gap-4;

  .dual-container-item {
    @apply basis-0 grow md:max-w-[calc(50%-var(--spacing))];
  }
}
</style>
