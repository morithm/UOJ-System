let defaults = {
  assetPacks: {
    beforeLoad: ['allAssetsMinusPlayer', 'playerAlex', 'playerAgent'],
    afterLoad: [],
  },
  gridDimensions: [10, 10],
  fluffPlane: ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
  playerName: 'Alex',
  playerStartPosition: [],
};

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  let regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

let levelParam = getParameterByName('level');
let testLevelToLoad = levels[levelParam] || levels['default'];
console.log(testLevelToLoad);
testLevelToLoad = Object.assign({}, defaults, testLevelToLoad);

// Initialize test instance of game, exposed to window for debugging.
let gameController = new GameController({
  Phaser: window.Phaser,
  containerId: 'code-game',
  assetRoot: '/js/code-dot-org/craft/assets/',
  audioPlayer: new Sounds(),
  debug: false,
  earlyLoadAssetPacks: testLevelToLoad.earlyLoadAssetPacks,
  earlyLoadNiceToHaveAssetPacks: testLevelToLoad.earlyLoadNiceToHaveAssetPacks,
  afterAssetsLoaded: () => {
    gameController.codeOrgAPI.resetAttempt();
  },
});


gameController.loadLevel(testLevelToLoad);

let $levelselect = $('#level-load');
Object.keys(levels).forEach(key => {
  $levelselect.append($('<option/>', { text: key, selected: key === levelParam }));
});

$levelselect.on('change', () => {
  location.search = `level=${$levelselect.val()}`;
});

$('input[type=range]').on('input', function() {
  $("#speed-display").html('Speed: ' + $(this).val() + 'x');
  gameController.game.time.slowMotion = 1.5 / parseFloat($(this).val(), 10);
});

$('#run-code').click(() => {
    let url = $('#code-game').attr('data-api-url')+'cmd';
  $.get(url, cmd => {
    if (cmd == null)
      console.log('cmd null');
    else {
      console.log(cmd);
      const api = gameController.codeOrgAPI;
      const target = 'Player';
      for (const ch of cmd) {
        if (ch === 'F') api.moveForward(null, target);
        if (ch === 'L') api.turnLeft(null, target);
        if (ch === 'R') api.turnRight(null, target);
        if (ch === 'D') api.destroyBlock(null, target);
        if (ch === 'U') api.use(null, target);
        if (ch === 'O') api.placeBlock(null, "planksOak", target);
        if (ch === 'B') api.placeBlock(null, "planksBirch", target); 
        if (ch === 'C') api.placeBlock(null, "cropWheat", target); 
        if (ch === 'T') api.placeBlock(null, "torch", target); 
        if (ch === 'c') api.placeInFront(null, "cobblestone", target); 
      }
      api.startAttempt();
    }
  });
});

// $('#fetch-level').click(() => {
//   let url = $('#code-game').attr('data-api-url')+'map';
//   console.log(url);
//   $.get(url, cmd => {
//     if (cmd == null)
//       console.log('cmd null');
//     else {
//       console.log(cmd);
//       location.search = `level=${cmd}`;
//     }
//   });
// });

$('#reset-button').click(function() {
  gameController.codeOrgAPI.resetAttempt();
  // gameController.codeOrgAPI.startAttempt();
});

if (!gameController.levelData.isAgentLevel) {
  $('#entity-select').hide();
}

window.gameController = gameController;