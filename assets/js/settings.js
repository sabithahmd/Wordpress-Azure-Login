function toggleConditionalFields( radioFieldName, fields) {
	const selectedValue = document.querySelector('input[name="'+radioFieldName+'"]:checked').value;
	fields.forEach((fieldData) => {
		const field = document.querySelector('input[name="'+fieldData[0]+'"]').closest('tr');
		if (selectedValue === fieldData[1]) {
			field.style.display = '';
		}
		else {
			field.style.display = 'none';
		}
	});
}