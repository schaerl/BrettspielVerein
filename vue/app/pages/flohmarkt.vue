<script setup lang="ts">
</script>

<template>
  <UContainer>
    <Section title="Flohmarkt am 26. September">
      <div>
        Wir freuen uns, euch zum ersten Mal einen Brettspielflohmarkt anbieten zu können! Hast du Spiele zu Hause, die ein neues Zuahuse suchen? Oder hast du Lust, ein neues Spiel in deine Sammlung zu holen? Dann melde dich entweder als Verkäufer an, oder komm vorbei! Am 26. September werden wir dies für euch möglich machen!
      </div>
      <br>
      <div>
        Neben einer grossen Auswahl an Spielen aller Grösse und Art, erwartet euch ausserdem unser Café, in welchem ihr euch mit Züpfe, Kaffee und Kuchen - bei schönem Wetter allenfalls auch mit Grilladen - verpflegen könnt.
      </div>
      <div class="header">
        Das Wichtigste in Kürze
      </div>
      <table class="mb-4">
        <tbody>
          <tr>
            <td>Wann?</td>
            <td>26. September, 10:30 - 17:30</td>
          </tr>
          <tr>
            <td>Wo?</td>
            <td>Spittelhof, Strengelbacherstrasse 29, 4800 Zofingen</td>
          </tr>
          <tr>
            <td>Was?</td>
            <td>Kartenspiele, Brettspiele und Zubehör</td>
          </tr>
          <tr>
            <td>Anmeldeschluss?</td>
            <td>23. September, 23:59</td>
          </tr>
        </tbody>
      </table>
      <div>
        Falls du bei uns Spiele verkaufen willst, dann melde dich bitte auf <NuxtLink
          class="hover-link"
          href="https://www.unserbasar.de/?bvzofingen"
        >unserbasar</NuxtLink> an. Alle nötigen Informationen zur Nutzung der Seite findet ihr <NuxtLink
          class="hover-link"
          href="https://www.unserbasar.de/basarsoftware.php#teilnehmer"
        >hier</NuxtLink>.
      </div>
      <div class="header">
        Verkaufsbedingungen
      </div>
      <div>
        <ul class="list-disc list-inside pb-2">
          <li>
            insgesamt 200 Nummern
          </li>
          <li>
            Nummernvergabe vom 20.07.2026 ab 10:00 Uhr
          </li>
          <li>
            Maximal 2 Nummern pro Verkäufer
          </li>
          <li>
            Maximal 50 Artikel pro Nummer
          </li>
        </ul>
        <div>
          Aus den Einnahmen werden 5.00 Fr pro Nummer sowie 15% des Verkaufserlöses einbehalten. Die Auszahlung wird auf 50 Rappen gerundet.
        </div>
        <div>
          Die Auszahlung des Erlöses erfolgt einige Tage nach dem Event via Twint.
        </div>
      </div>
      <br>
      <div>
        Falls du irgendwelche Fragen hast, sei es zum Event an sich, oder zum Verkauf, schreib eine Email an <NuxtLink
          class="hover-link"
          href="mailto:versand@brettspiel-zofingen.ch"
        >versand@brettspiel-zofingen.ch</NuxtLink>, oder verwende unser <NuxtLink
          class="hover-link"
          to="/#kontakt"
        >Kontaktformular</NuxtLink>.
      </div>
      <div>
        Lasst euch diese Gelegenheit nicht entgehen! Wir freuen uns auf euer erscheinen.
      </div>
    </Section>
  </UContainer>
</template>

<style scoped>
td:first-child {
  font-weight: bold;
  padding-right: 16px;
}

a {
  color: var(--color-header-500);
  text-decoration: underline;
}

.header {
  padding-top: 12px;
  text-decoration: underline;
  font-weight: bold;
  font-size: 1.2rem;
}
</style>
