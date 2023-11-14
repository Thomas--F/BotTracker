<template>
  <div v-content-intro>
    <h2>{{ translate('BotTracker_BotTracker') }}</h2>
    <p>{{ translate('BotTracker_PluginDescription') }}</p>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { ContentIntro } from 'CoreHome';

export default defineComponent({
  directives: {
    ContentIntro,
  },
});
</script>
