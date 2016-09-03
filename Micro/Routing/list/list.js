var	header     = 2;
var numericCells = [1];

var table      = document.getElementById('table');
var sort       = document.getElementById('sort');
var inputFile  = document.getElementById('input_file');
var inputName  = document.getElementById('input_name');
var inputParts = document.getElementById('input_parts');
var inputRoute = document.getElementById('input_route');
var inputController = document.getElementById('input_controller');
var trTag 	   = document.getElementsByTagName('tr');
var checkbox   = document.querySelectorAll('.but');

var sortCellNumber;
var sortRowNumber;
var realRowIndex;
var lastRowIndex;
var rowsObject = {};
var cellsArray = [];
var	sortFlag   = [];
var lastIndex  = 1;
var rowsCount  = table.rows.length - header;
var cellsCount = table.rows[header + 1].cells.length

for (c = 0; c < cellsCount; c++) {
	cellsArray[c] = [];
	  sortFlag[c] = 1;
	
	for (r = 0; r < rowsCount; r++) {
		cellsArray[c][r] = [];
		cellsArray[c][r]['value'] = table.rows[r + header].cells[c].innerHTML;
		cellsArray[c][r]['number'] = r;
		cellsArray[c][r]['hide'] = 0;
	}
}
function inArray(elem, array) {
	for (i = 0; i < array.length; i++) {
		if (array[i] == elem) {
			return true;
		}
	}
	return false;
}

function compare(order, num) {
	if (order != 'desc') {
		if (!num) {
			return function compareAsc(a, b) {
				if (a.value == '') return 1;
				if (b.value == '') return -1;
				if (a.value > b.value) return  1;
				if (a.value < b.value) return -1;
			}
		} else {
			return function compareAscNumeric(a, b) {
				if (a.value == '') return 1;
				if (b.value == '') return -1;
				return  a.value - b.value;
			}
		}
	} else {
		if (!num) {
			return	function compareDesc(a, b) {
				if (a.value > b.value) return -1;
				if (a.value < b.value) return  1;
			}
		} else {
			return	function compareDescNumeric(a, b) {
				return  b.value - a.value;
			}
		}
	}
}

sort.onclick = function(event) {
	var target = event.target;
	if (target.tagName != 'TH') {
		target = target.parentNode;
	}

	if (sortFlag[target.cellIndex] == 1) {
		cellsArray[target.cellIndex].sort(compare('asc', inArray(target.cellIndex, numericCells)));
		target.innerHTML = '&#8593';
		target.classList.add('sort_pointer');
	}
	else {
		cellsArray[target.cellIndex].sort(compare('desc', inArray(target.cellIndex, numericCells)));
		target.innerHTML = '&#8595';
	}

	if (target.cellIndex != lastIndex) {
		target.parentNode.children[lastIndex].innerHTML = '';
		target.parentNode.children[lastIndex].classList.remove('sort_pointer');
		sortFlag[lastIndex] = 1;
	}
	lastIndex = target.cellIndex;
	sortFlag[target.cellIndex] = sortFlag[target.cellIndex] * -1;
	tableRewrite();
}

function  tableRewrite() {
	if(lastRowIndex) {
		table.rows[lastRowIndex].classList.remove('highlight');
	}
	rowsObject = {};
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
			table.rows[r + header].classList.add('hidden');
		}
		else {
			table.rows[r + header].classList.remove('hidden');
		}

		sortCellNumber = table.rows[r + header].cells[1].innerHTML;
		sortRowNumber = cellsArray[lastIndex][r]['number'] + 1;

		if (sortCellNumber != sortRowNumber) {
			rowsObject[sortCellNumber] = table.rows[r + header].innerHTML;

			if (sortRowNumber in rowsObject) {
				table.rows[r + header].innerHTML = rowsObject[sortRowNumber];
			}
			else {
				for (s = r + header; s < table.rows.length; s++) {

					if (table.rows[s].cells[1].innerHTML == sortRowNumber) {
						table.rows[r + header].innerHTML = table.rows[s].innerHTML;
						break;
					}
				}
			}
		}
		if (cellsArray[lastIndex][r]['number'] == realRowIndex) {
			table.rows[r + header].classList.add('highlight');
			lastRowIndex = r + header;
		}
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

inputFile.onkeyup = function() {
	inputHandler(inputFile.value, 0);
}

inputName.onkeyup = function() {
	inputHandler(inputName.value, 2);
}

inputParts.onkeyup = function() {
	inputHandler(inputParts.value, 3);
}

inputRoute.onkeyup = function() {
	inputHandler(inputRoute.value, 13);
}

inputController.onkeyup = function() {
	inputHandler(inputController.value, 14);
}

for (i = header; i < trTag.length; i++) {
	
	trTag[i].onclick = function(event) {
		realRowIndex = cellsArray[lastIndex][this.rowIndex - header]['number'];

		if (lastRowIndex) {
			table.rows[lastRowIndex].classList.remove('highlight');
		}
		lastRowIndex = this.rowIndex;
		this.classList.add('highlight');
	}
}

for (k = 0; k < checkbox.length; k++) {

	checkbox[k].onclick = function() {

		for (i = header; i < table.rows.length; i++) {
			table.rows[i].cells[this.getAttribute('data-cell')].classList.toggle('minimize');
		}
	}
}
console.dir(checkbox);