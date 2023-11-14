<template>
  <ContentBlock content-title="My title" help-url="https://matomo.org">
  </ContentBlock>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { ContentBlock } from 'CoreHome';

export default defineComponent({
  components: {
    ContentBlock,
  },
});
</script>
