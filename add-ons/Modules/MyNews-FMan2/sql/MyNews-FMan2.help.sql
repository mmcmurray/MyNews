LOCK TABLES `help` WRITE;
INSERT INTO `help` VALUES
	('','fman2','formmode1','When a filename already exists in the system, this will move the original file to an archive file with an incremental extension.\r\n\r\n<b>For example:</b>\r\n- file1 already exists.\r\n- original file1 is moved to file1.1\r\n- {uploaded file} is saved as file1'),
	('','fman2','formmode2','When a filename already exists in the system, this will leave the original file as is, and name the newly uploaded file with an incremental extenstion:\r\n\r\n<b>For example:</b>\r\n- file1 already exists.\r\n- {uploaded file} -> file1.1'),
	('','fman2','formmode3','If the file already exists, this option will remove the original and place the newly uploaded file in it\'s place.'),
	('','fman2','formmode4','If the file already exists, this option will return an error informing the user that it already exists and that no action was taken.'),
	('','fman2','uploadfile','Browse for the file you would like to upload to the server.'),
	('','fman2','createdir','Input a name and submit the form to create a new sub-directory.');
UNLOCK TABLES;
