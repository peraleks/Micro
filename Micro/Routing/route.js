window.onload = function () {
	var table      = document.body.children[0];
	var sort       = document.getElementById('sort');
	var inputFile  = document.getElementById('input_file');
	var inputName  = document.getElementById('input_name');
	var inputParts = document.getElementById('input_parts');
	var inputRoute = document.getElementById('input_route');
	var rowsArray  = [];
	var cellsArray = [];
	var	header     = 2;
	var rowsCount  = table.rows.length - header;
	var cellsCount = table.rows[header + 1].cells.length
	var	sortFlag   = [];
	var lastIndex;

	for (c = 0; c < cellsCount; c++) {
		cellsArray[c] = [];
		  sortFlag[c] = 1;
		
		for (r = 0; r < rowsCount; r++) {
			if (c == 0) {
				rowsArray[r] = [];
				rowsArray[r] = table.rows[r + header].innerHTML;
			}
			cellsArray[c][r] = [];
			cellsArray[c][r]['value'] = table.rows[r + header].cells[c].innerHTML;
			cellsArray[c][r]['number'] = r;
			cellsArray[c][r]['hide'] = 0;
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
			cellsArray[target.cellIndex].sort(compareAsc);
			target.innerHTML = '<span style="color: #ff0;">&#8593</span>';
			target.style.background = '#000';
		}
		else {
			cellsArray[target.cellIndex].sort(compareDesc);
			target.innerHTML = '<span style="color: #ff0;">&#8595</span>';
		}
		if (lastIndex != undefined) {
			if (target.cellIndex != lastIndex) {
				target.parentNode.children[lastIndex].innerHTML = '';
				target.parentNode.children[lastIndex].style.background = '#444';
				sortFlag[lastIndex] = 1;
			}
		}
		lastIndex = target.cellIndex;
		sortFlag[target.cellIndex] = sortFlag[target.cellIndex] * -1;
		tableRewrite();
	}

	function  tableRewrite() {
		for (r = 0; r < rowsCount; r++) {
			var hide = 0;

			for (c = 0; c < cellsCount; c++) {

				for (h = 0; h < rowsCount; h++) {

					if (cellsArray[lastIndex][r]['number'] == cellsArray[c][h]['number']) {
						hide += cellsArray[c][h]['hide'];
					}
				}
			}
			if (hide > 0) {
				table.rows[r + header].style.display = "none";
			}
			else {
				table.rows[r + header].style.display = "table-row";
			}
			table.rows[r + header].innerHTML = rowsArray[cellsArray[lastIndex][r]['number']];
		}
	}

	function inputHandler(value, cell) {
		for (i = 0; i < rowsCount; i++) {
			if (value == '') {
				cellsArray[cell][i]['hide'] = 0;
			}
			else if (cellsArray[cell][i]['value'].search(value) < 0) {
				cellsArray[cell][i]['hide'] = 1;
			}
			else {
				cellsArray[cell][i]['hide'] = 0;
			}
		}
		tableRewrite();
	}

	inputFile.onkeyup = function(event) {
		inputHandler(inputFile.value, 0);
	}

	inputName.onkeyup = function(event) {
		inputHandler(inputName.value, 1);
	}

	inputParts.onkeyup = function(event) {
		inputHandler(inputParts.value, 2);
	}

	inputRoute.onkeyup = function(event) {
		inputHandler(inputRoute.value, 12);
	}

};
