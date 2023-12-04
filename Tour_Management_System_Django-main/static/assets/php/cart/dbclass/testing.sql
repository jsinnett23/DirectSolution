-- --------------------------------------------------------

--
-- Table structure for table `tblitems`
--

CREATE TABLE tblitems (
  recid int(11) NOT NULL AUTO_INCREMENT,
  item_number varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  item_name varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  item_title varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  item_description text COLLATE utf8_unicode_ci,
  item_description_full text COLLATE utf8_unicode_ci,
  mc_gross varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  item_image varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  publish int(11) NOT NULL DEFAULT '1',
  sortorder int(11) NOT NULL DEFAULT '100',
  pagetitle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  metadescription text COLLATE utf8_unicode_ci,
  PRIMARY KEY (recid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblitems`
--
ALTER TABLE tblitems
  ADD UNIQUE KEY `item_number` (`item_number`),
  ADD KEY `item_name` (`item_name`);



--
-- Dumping data for table `tblitems`
--
INSERT INTO tblitems (item_number, item_name, item_title, item_description, item_description_full, mc_gross, item_image, publish, sortorder, pagetitle, metadescription) 
VALUES 
('Cork', 'Cork', 'Cork', "What do you get when you take the cork out of a bottle, you get a nice pop sound that makes everyone jump.", "Pop open a bottle and hopefully the cork will shoot up to the ceiling. If it doesn’t then you may feel a little disappointed.A dull dud pop is what you may get.  The fizz will give it the pop so do we think that the bubbly is not quite as good if we don't get a good sound.",  '0.01', 'images/cork.jpg', '1', '100', 'Cork', NULL),
('Dial Up', 'Dial Up', 'Dial Up', "Do you remember the kind of sound you use to get in the old days on phone line when dialing up with a modem.", "This is probably a sound that people only of a certain age will remember.  It is the sound of the start of the internet and you would hear it when you made you attempt to connect at some real slow data rate.",  '0.02', 'images/dialup.jpg', '1', '100', 'Dial Up', NULL),
('Bottle', 'Bottle', 'Bottle', "Bottles produce a nice sound when opened and then the contents poured out.", "The metal cap is prised off the top of the bottle and it falls to the fall bouncing around.",  '0.03', 'images/bottle.jpg', '1', '100', 'Bottle', NULL),
('Door Bell', 'Door Bell', 'Door Bell', "Oh my word, oh my gosh, its a ding dong door bell.  Probably battery operated with those big D type batteries pushed into the plastic housing.", "I have to say they are better than those wireless types that you can hardly hear.  You are in your house snorring away or having a cup of coffee, and the door bell sounds.  The dog jumps up and runs around barking and it is someone trying to sell you replacement windows.",  '0.04', 'images/doorbell.jpg', '1', '100', 'Door Bell', NULL),
('Drip', 'Drip', 'Drip', "This is the constant drip, drip, drip of water falling into a pool or container.  I don't think I have heard it quite like this as it does sound a little loud.  It would drive you crazy like this.", "How to fix a leaking tap - first, you will want to turn off the supply to that tap by isolating the value closest to the tap.  If you can't do that then you may ahe to turn off the water supply to the house at the mains.  With ceramic disc taps, you will have to replace the whole valve but with with rubber washer taps, you only need to replace the washer itself which can be very cheap to purchase.",  '0.05', 'images/drip.jpg', '1', '100', 'Drip', NULL),
('Coffee', 'Coffee', 'Coffee', "Expensive coffee machines make those really nice sounds like that you hear in one of those fancy coffee shops in town.", "Apparantly coffee machines come in all sorts of types and costs.  The general principle is just put some coffee grounds in a filter and then when the water heats up in the kettle, the hot water is poured over the beans but as a series of drips. ",  '0.06', 'images/coffee.jpg', '1', '100', 'coffee', NULL),
('Message', 'Message', 'Message', "Do you remember the sound that exemplifies the early days of mobile phones.  It is the sound of an incoming text message.", "It is so characteristic of the era and when the sound comes through every one in the room looks at their mobile phone as they think it is a message for them.",  '0.07', 'images/message.jpg', '1', '100', 'Message', NULL),
('Ring Tone', 'Ring Tone', 'Ring Tone', "You may remember this sound from early mobile phone days.", "Before the days of smart phones the ring tones that were available were quite basic but very distinctive as this audio sound clip illustrates.  If you are of a certain age you will probably be familiar with this audio clip.",  '0.08', 'images/phone.jpg', '1', '100', 'Ring Tone', NULL);



CREATE VIEW view_publisheditems AS
	SELECT
	tblitems.recid, tblitems.item_number,
	tblitems.item_name, tblitems.item_title, tblitems.item_description, 
    tblitems.item_description_full, tblitems.mc_gross,
	tblitems.item_image,  tblitems.publish, tblitems.sortorder,
    tblitems.pagetitle, tblitems.metadescription
	FROM
	tblitems
	WHERE publish = 1
	ORDER BY tblitems.sortorder;



