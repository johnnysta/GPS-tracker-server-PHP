# GPS-tracker-server-PHP
PHP application to receive and store GPS coorintates in a database, then display these coordinates on a Google map.
GPS coordinates are received from a device (e.g. an android phone: see GPS-tracker-android-client).
Users can register to the server, then they receive an ID that needs to be entered in the android client app's settings.
After that, the server stores the coordinate info sent by the android app.
One user can record coordinates from more devices (entities).
Then the users can log in, and display the tracking map for the selected entity.
