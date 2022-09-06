document.addEventListener(
  "DOMContentLoaded", () => {
    var typingTimer;
    var typeInterval = 500;

    document.querySelectorAll(".dmSearchFileField").forEach(function (item) {
      item.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(liveDownSearch(item), typeInterval);
      });
    });

    function liveDownSearch(item) {
      var currentSearchVal = item.value;
      var currentSearchValToLower = currentSearchVal.toLowerCase();
      var searchWrapId = item.dataset.searchid;
      var items = document.querySelectorAll('#' + searchWrapId + ' .list-item')
      items.forEach(function (i) {
        var filterText = i.dataset.filtertext;
        var filterTextToLower = i.dataset.filtertext.toLowerCase();
        if (currentSearchVal === '') {
          i.classList.add("d-none");
        } else if (currentSearchVal === '' || filterText.includes(currentSearchVal) || filterTextToLower.includes(currentSearchValToLower)) {
          i.classList.remove("d-none");
        } else {
          i.classList.add("d-none");
        }
      });
    }
  }
);
