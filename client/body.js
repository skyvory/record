if(Meteor.isClient) {
	Template.body.helpers({
		vn: function() {
			return Visuals.find({}, {sort: {date_created: 1} });
		}
	});

	Template.body.events({
		"submit .add-vn": function(event) {
			event.preventDefault();

			var input = event.target.title;
			Meteor.call("addVn", input.value);
			input.value = "";
		}
	});
}