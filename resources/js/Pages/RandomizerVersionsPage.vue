<template>
  <div class="card content-div mt-3 mb-3">
    <div class="card-header">
      <h4>Randomizer Version Differences</h4>
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">
        There are currently four main randomizers:
        <ul>
          <li>Jangler's Randomizer: (<a href="https://github.com/jangler/oracles-randomizer">github</a>, <a href="https://cemulate.github.io/oracles-randomizer-web/">Web UI</a>)</li>
          <li>Entrance Randomizer: (<a href="https://github.com/vinheim3/oracles-randomizer">github</a>, <a href="https://cemulate.github.io/oracles-randomizer-web/">Web UI</a> -- select entrance-rando version)</li>
          <li>Stewmath's Randomizer: (<a href="https://github.com/Stewmath/oracles-randomizer-ng">github</a>, <a href="https://oosarando2.zeldahacking.net/">Web UI</a>)</li>
          <li>Karafruit's Randomizer: (<a href="https://github.com/ardnaxelarak/oracles-randomizer-ng-plus">github</a>, <a href="https://oracles-dev.gwaa">Web UI</a>)</li>
        </ul>
      </li>
      <li class="list-group-item">
        <p><b>Jangler's Randomizer</b></p>
        <ul>
         <li>The original version of the randomizer, upon which all the other versions are based</li>
         <li>Tree Warp: If enabled, press Start while closing the overworld map to warp to the seed tree in the starting village (ember tree in vanilla)</li>
         <li>Currently the only version to support multiworld</li>
         <li>Supports dungeon shuffle, as well as portal shuffle in Seasons</li>
         <li>Treasure map in Seasons only shows treasures in the overworld; treasure in Subrosia are marked on the screen of the nearest portal</li>
         <li>In Ages, giving mystery seeds to Queen Ambi is done via a red soldier</li>
         <li>In Ages, the "brother emblem" Goron dance check can be gotten in the past only if you don't have the letter of introduction; it can always be gotten in the present</li>
         <li>In Ages, if you've cured the seas before talking to King Zora in the present for the first time, you need to leave the room and re-enter to get permission to enter Jabu-Jabu</li>
        </ul>
      </li>
      <li class="list-group-item">
        <p><b>Entrance Randomizer</b></p>
        <p>Mostly the same as Jangler's randomizer, with a few additional features:</p>
        <ul>
          <li>Entrances are fully shuffled</li>
          <li>Standing heart pieces are also shuffled</li>
          <li>No multiworld support</li>
        </ul>
      </li>
      <li class="list-group-item">
        <p><b>Stewmath's Randomizer</b></p>
        <p>Still based on Jangler's randomizer, but with several changes:</p>
        <ul>
          <li>Warp to Start replaces Tree Warp: Always enabled, accessed by pressing select twice in succession</li>
          <li>The advance shops are open instead of closed, but the contents are not randomized</li>
          <li>Treasure Map in Seasons works in Subrosia as well, will show sparkles on the Subrosia world map for treasures in Subrosia</li>
          <li>Ages:
            <ul>
              <li>D2 past/present entrance could lead to different dungeons in dungeon shuffle, with a randomly-picked dungeon having two entrances</li>
              <li>Giving mystery seeds to Queen Ambi is done through a standing red soldier on the same screen as you'd turn them in on vanilla</li>
              <li>The Goron dance check where you'd get the brother emblem is always your first Goron dance, whether in past or present; the second item (requiring the letter of introduction) can still only be done in the past once the first check has been obtained</li>
              <li>If you've cured the seas before talking to King Zora in the present, you can talk to him twice without leaving the room to get permission to enter Jabu-Jabu</li>
            </ul>
          </li>
          <li>Supports cross items (seasons-exclusive items in ages, and ages-exclusive items in seasons)</li>
          <li>Supports keysanity (shuffling dungeon items outside of their dungeon); compasses will play a tune corresponding to the dungeon when you enter a room with a key, and dungeon items for all dungeons can be viewed in the randomizer menu by pressing select twice</li>
          <li>Auto mermaid option: when selected, holding a direction key with the mermaid suit will continually propel link in the held direction instead of needing to be tapped</li>
          <li>Flute and Harp music can be skipped by pressing A+B, allowing faster transitions between past and present in Ages.</li>
        </ul>
      </li>
      <li class="list-group-item">
        <p><b>Karafruit's Randomizer</b> (the one you're viewing right now)</p>
        <p>Based on Stewmath's randomizer, so all the above changes still apply, with additional changes:</p>
        <ul>
          <li>Option to include items acquired from a linked game (biggoron's sword, additional sword, shield, and satchel upgrades)</li>
          <li>Starting items can be selected; however currently only sword, biggoron's sword, and seasons treasure map are supported by the web UI</li>
          <li>Allows reducing the number of essences required to obtain the maku seed</li>
          <li>Allows adding Maple's heart piece into the pool; if selected, the random item will always show up every time you encounter Maple until you obtain it; it will be the first item Maple goes after</li>
          <li>Allows adding the heart piece from gasha nuts into the pool; if selected, the random item will be obtained from your first gasha nut</li>
          <li>Option to reduce the fool's ore from the vanilla 12 damage to a more balanced 4 damage, or remove it from the pool entirely</li>
          <li>The Strange Brothers in Subrosia will no longer steal your feather; following them will result in obtaining the usual prizes for following them after you recover your feather</li>
        </ul>
      </li>
    </ul>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
});
</script>

<style scoped>
.content-div {
  width: 50rem;
  margin: auto;
}
</style>
