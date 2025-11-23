<script setup lang="ts">
//const { xs } = useDisplay();

const goTo = useOffsetGoTo();

const { repository: eventRepository } = useEventRepository();
const { data } = useLazyAsyncData(() => eventRepository.getEventData(), {
  server: false,
});

const { navReceiver } = defineProps<{ navReceiver: HeaderReceiver }>();
const home = useTemplateRef("home");
const sectionUs = useTemplateRef("section_us");
const sectionMembership = useTemplateRef("section_membership");
const sectionEvents = useTemplateRef("section_events");
const sectionContact = useTemplateRef("section_contact");

const { addAHeader } = useNavHeaderProvider(navReceiver);
addAHeader("Home", home);
addAHeader("Über uns", sectionUs);
addAHeader("Mitgliedschaft", sectionMembership);
addAHeader("Events", sectionEvents);
addAHeader("Kontakt", sectionContact);

const { alertData, triggerAlert } = useAlert();
</script>

<template>
  <div ref="home">
    <AlertContainer :alertData="alertData"></AlertContainer>
    <ImageContainer :variant="1">
      <UContainer>
      <!--<VRow><WelcomeBanner></WelcomeBanner></VRow> -->
      <!--  <VRow class="ga-4 mt-6">
          <UButton
            color="primary"
            elevation="0"
            variant="flat"
            rounded="pill"
            @click="goTo(sectionUs!)"
            >Über uns
          </UButton>
          <UButton
            color="black"
            elevation="0"
            variant="text"
            rounded="pill"
            @click="goTo(sectionMembership!)"
            >Werde Mitglied</UButton
          >
      </VRow> -->
      </UContainer>
    </ImageContainer>

    <Section ref="section_us" :title="'Über uns'">
      <div>
        Wir treffen uns 1x im Monat jeweils an einem Freitagabend im Spittelhof
        Zofingen und spielen Brettspiele jeder Art. Von gehobenen
        Familienspielen, bis mehrstündigen Expertenspielen ist für Jeden etwas
        dabei! Spielerfahrung ist von Vorteil, aber absolut nicht erforderlich.
        Die Teilnahme an unserem Spieleabend steht allen Altersgruppen offen.
        Bei uns werden einige Spiele zur Verfügung gestellt und erklärt. Es
        dürfen aber sehr gerne auch eigene Spiele mitgebracht werden! Perfekt
        also für alle, die zu Hause wegen fehlenden Mitspielern noch ungespielte
        Spiele rumliegen haben, die schon lange wieder mal ein bestimmtes Spiel
        spielen wollten und natürlich auch für alle anderen, die einfach gerne
        Spiele spielen.
      </div>
    </Section>

    <ImageContainer :variant="2">
      <div>
        <h1>Abonniere unseren Newsletter</h1>
        <p>Sei stets informiert über die nächsten Events!</p>
        <NewsletterForm
          class="mt-4"
          @on-submission="(event: AlertStatus) => triggerAlert(event)"
        >
        </NewsletterForm>
      </div>
      <template #right>
        <WhatsappCode></WhatsappCode>
      </template>
    </ImageContainer>
    <UContainer>
      <Section ref="section_membership" title="Vereinsmitgliedschaft">
	<!-- <VRow>
          <VCol>
            <h2 class="mb-4">Mitglieschaftsvorteile</h2>
            <MembershipTable></MembershipTable>
          </VCol>
          <VCol>
            <h2 class="mb-4">Anmeldung Mitgliedschaft</h2>
            <SignUpForm @on-submission="(event: AlertStatus) => triggerAlert(event)"></SignUpForm>
          </VCol>
	</VRow> -->
      </Section>
      <Section ref="section_events" title="Die nächsten Veranstaltungen">
       <!-- <VSheet color="secondary" class="pa-4" rounded="xl">
          <VRow class="flex-column">
            <VCol v-for="eventData in data" :key="eventData.id">
              <EventCard :data="eventData"> </EventCard>
            </VCol>
          </VRow>
       </VSheet>-->
      </Section>
      <Section ref="section_contact" title="Kontakt">
        <!-- <VRow>
          <VCol :cols="xs ? 12 : 6">
            <h2 class="mb-2">Fragen?</h2>
            <VSheet color="secondary" class="pa-4" rounded="xl">
              <QuestionForm
                @on-submission="(event: AlertStatus) => triggerAlert(event)"
              ></QuestionForm>
            </VSheet>
          </VCol>
          <VCol>
            <h2 class="mb-2">Wo spielen wir?</h2>
            <MapWrapper></MapWrapper>
          </VCol>
	</VRow>-->
      </Section>
    </UContainer>
  </div>
</template>
