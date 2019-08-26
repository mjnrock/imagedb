function CRUD_AJAX(table, action, payload = null, condition = null, callback) {
	callback = !!callback ? callback : function(e){};
	$.ajax({
		url: `/api/db/CRUD.php`,
		data: {
			TableName: table,
			Action: action,
			Payload: payload,
			Condition: condition
		},
		success: callback
	});
}