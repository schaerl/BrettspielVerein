<script setup lang="ts">
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LControl, LMarker, LPopup } from "@maxel01/vue-leaflet";
</script>

<template>
  <div class="map_canvas rounded-xl">
    <LMap
      :zoom="15"
      :center="[47.28497748738651, 7.942945230383572]"
      :use-global-leaflet="false"
    >
      <LTileLayer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        attribution="&amp;copy; <a href=&quot;https://www.openstreetmap.org/&quot;>OpenStreetMap</a> contributors"
        layer-type="base"
        name="OpenStreetMap"
      />
      <LControl>
        <div class="bg-white p-2 shadow flex flex-col gap-1">
          <div class="font-bold">
            Spittelhof
          </div>
          <div>
            <div>Strengelbacherstrasse 29</div>
            <div>4800 Zofingen</div>
          </div>
          <NuxtLink
            class="cursor-pointer"
            href="https://www.google.ch/maps/dir//Freizeitverein+Spittelhof,+Strengelbacherstrasse+29,+4800+Zofingen/@47.282451,7.8668482,13z"
          >
            Route in Google Maps öffnen
          </NuxtLink>
        </div>
      </LControl>
      <LMarker
        :lat-lng="[47.28247748738651, 7.938945230383572]"
      >
        <LPopup>Spittelhof, Strengelbacherstrasse 29, 4800 Zofingen</LPopup>
      </LMarker>
    </LMap>
  </div>
</template>

<style scoped>
.map_canvas {
  overflow: hidden;
  height: 300px;
  width: 100%;
}
</style>
