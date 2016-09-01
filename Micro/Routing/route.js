window.onload = function () {
	var table = document.body.children[0];
	var sort = document.getElementById('sort');
	var rowsTable = [];
	var cellsTable = [];
	var	sortFlag = [];
	var	header = 2;
	var cellsLength = table.rows[header + 1].cells.length
	var lastIndex;
	for (c = 0; c < cellsLength; c++) {
		cellsTable[c] = [];
		sortFlag[c] = 1;
		for (i = header; i < table.rows.length; i++) {
			if (c == 0) {
				rowsTable[i] = [];
				rowsTable[i] = table.rows[i].innerHTML;
			}
			cellsTable[c][i - header] = [];
			cellsTable[c][i - header]['value'] = table.rows[i].cells[c].innerHTML;
			cellsTable[c][i - header]['number'] = i;
		}
	}
	function compareAsc(a, b) {
		if (a.value == '') return 1;
		if (b.value == '') return -1;
		if (a.value > b.value) return  1;
		if (a.value < b.value) return -1;
	}
	function compareDesc(a, b) {
		if (a.value > b.value) return -1;
		if (a.value < b.value) return  1;
	}
	sort.onclick = function(event) {
		var target = event.target;
		if (target.tagName != 'TH') {
			target = target.parentNode;
		}
		if (sortFlag[target.cellIndex] == 1) {
			cellsTable[target.cellIndex].sort(compareAsc);
			target.innerHTML = '<span style="color: #ff0;">&#8593</span>';
			target.style.background = '#000';
		}
		else {
			cellsTable[target.cellIndex].sort(compareDesc);
			target.innerHTML = '<span style="color: #ff0;">&#8595</span>';
		}
		if (lastIndex != undefined) {
			console.dir(target.parentNode.children[lastIndex].innerHTML);
			if (target.cellIndex != lastIndex) {
				target.parentNode.children[lastIndex].innerHTML = '';
				target.parentNode.children[lastIndex].style.background = '#444';
				sortFlag[lastIndex] = 1;
			}
		}
		lastIndex = target.cellIndex;
		sortFlag[target.cellIndex] = sortFlag[target.cellIndex] * -1;

		for (var i = header; i <= table.rows.length - 1; i++) {
			table.rows[i].innerHTML = rowsTable[cellsTable[target.cellIndex][i - header]['number']];
		}
	}
	console.dir(rowsTable);
	console.dir(cellsTable);

};
