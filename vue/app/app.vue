<script setup lang="ts">
//const goTo = useOffsetGoTo();

const { headers, receiver } = useNavHeaderReceiver();

const drawerVisibility = ref(false);
//const { mdAndUp } = useDisplay();
</script>

<template>
  <UApp>
    <UHeader :elevation="0" color="header">
      <UContainer>
      <!-- <VRow class="align-center justify-space-between px-4"> -->
          <LogoTextWrapper onPrimary></LogoTextWrapper>
          <div ref="test" class="nav-container" v-if="headers?.length !== 0">
            <!--template v-if="mdAndUp"-->
            <template v-if="true">
              <!--div
                v-for="sec in headers"
                class="hover-link d-inline mx-2"
                @click="goTo(unref(sec.goal.ref)!)"
                :class="{ 'scrolled-over': sec.goal.isScrolledOver }"
              -->
              <div
                v-for="sec in headers"
                class="hover-link d-inline mx-2"
                :class="{ 'scrolled-over': sec.goal.isScrolledOver }"
              >
                {{ sec?.displayName }}
              </div>
            </template>
            <UButton
              v-else
              @click.stop="drawerVisibility = !drawerVisibility"
              icon="mdi-menu"
            ></UButton>
          </div>
	  <!-- </VRow> -->
      </UContainer>
    </UHeader>
    <!--<VNavigationDrawer v-model="drawerVisibility" location="right" temporary>
      <VList class="nav-container">
        <VListItem
          v-for="sec in headers"
          @click="goTo(unref(sec.goal.ref)!)"
          class="hover-link"
          :class="{ 'scrolled-over': sec.goal.isScrolledOver }"
        >
          {{ sec.displayName }}
        </VListItem>
      </VList>
    </VNavigationDrawer> -->
    <UMain ref="myself">
      <NuxtPage :navReceiver="receiver"></NuxtPage>
    </UMain>
    <UFooter class="footer pa-0">
      <PageFooter></PageFooter>
    </UFooter>
  </UApp>
</template>
<style scoped>
.footer {
  height: 100%;
  min-height: 240px;
  display: relative;
}

.nav-container :nth-last-child(1 of .scrolled-over) {
  color: rgb(var(--v-theme-primary));
}
</style>
