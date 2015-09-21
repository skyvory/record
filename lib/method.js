Meteor.methods({
	addVn: function(title) {
		if(title_en === "") {
			throw new Meteor.Error("not-authorized");
		}
		// title = title.toLowerCase();

		//kill duplicates
		if(Visuals.findOne({title_en: title_en})) {
			return;
		}

		Visuals.insert({
			title_en: title_en,
			title_jp: "",
			hashtag: "",
			developer: "",
			date_release: "",
			date_start: "",
			date_end: "",
			story: "",
			naki: "",
			nuki: "",
			graphic: "",
			score: "",
			notes: [
				{
					interface: "",
					general: "",
					setting: "",
					other_chara: "",
					story: "",
					route: "",
					bgm: "",
					terminology: "",
					timescape: ""
				}
			],
			chara: [
				{
					kanji: "",
					betsumyou: "",
					yobikata: "",
					note: "",
					mark: "",
					snapshot: ""
				}
			],
			archive_savedata: "",
			date_created: "",
			date_modified: ""
		});
	}
});