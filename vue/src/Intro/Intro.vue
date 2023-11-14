<template>
  <div v-content-intro>
    <h2>{{ translate('BotTracker_BotTracker') }}</h2>
    <p>{{ translate('BotTracker_PluginDescription') }}</p>
  </div>

  <div
        class="sites_autocomplete"
      >
        <SiteSelector
           v-model="site"
           :show-selected-site="true"
           :switch-site-on-select="false"
           :show-all-sites-item="false"
           :showselectedsite="true"
           id="defaultReportSiteSelector"
        />
      </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { ContentIntro, SiteSelector } from 'CoreHome';

export default defineComponent({
  directives: {
    ContentIntro,
  },
});
</script>
