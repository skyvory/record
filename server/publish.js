if(Meteor.isServer) {
	Meteor.publish("vn", function() {
		return Visuals.find();
	});
}