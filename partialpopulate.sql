INSERT INTO GymUser values(1, 'aa@gmail.com', 'Aaron Aiken', '111-111-1111');
INSERT INTO GymUser values(2, 'bb@gmail.com', 'Bob Biles', '222-222-2222');
INSERT INTO GymUser values(3, 'cc@gmail.com', 'Camry Cain', '333-333-3333');
INSERT INTO GymUser values(4, 'dd@gmail.com', 'Dan Dough', '444-444-4444');
INSERT INTO GymUser values(5, 'ee@gmail.com', 'Erin Eagles', '555-555-5555');
INSERT INTO GymUser values(6, 'ff@gmail.com', 'Felicia Fierce', '666-666-6666');
INSERT INTO GymUser values(7, 'gg@gmail.com', 'Gary Guiles', '777-777-7777');
INSERT INTO GymUser values(8, 'hh@gmail.com', 'Henry Ham', '888-888-8888');
INSERT INTO GymUser values(9, 'ii@gmail.com', 'Indica Isles', '999-999-9999');
INSERT INTO GymUser values(10, 'jj@gmail.com', 'Jessie James', '101-101-1010');
INSERT INTO GymUser values(11, 'kk@gmail.com', 'Keiko Kristensen', '111-111-1111');
INSERT INTO GymUser values(12, 'll@gmail.com', 'Laila Lam', '121-121-1212');
INSERT INTO GymUser values(13, 'mm@gmail.com', 'Morey Manila', '131-131-1313');
INSERT INTO GymUser values(14, 'nn@gmail.com', 'Nate Noriega', '141-141-1414');
INSERT INTO GymUser values(15, 'oo@gmail.com', 'Oban Osterson', '151-151-1515');


INSERT INTO Athlete values(1);
INSERT INTO Athlete values(2);
INSERT INTO Athlete values(3);
INSERT INTO Athlete values(4);
INSERT INTO Athlete values(5);


INSERT INTO Trainer values(6);
INSERT INTO Trainer values(7);
INSERT INTO Trainer values(8);
INSERT INTO Trainer values(9);
INSERT INTO Trainer values(10);


INSERT INTO GymAdmin values(11);
INSERT INTO GymAdmin values(12);
INSERT INTO GymAdmin values(13);
INSERT INTO GymAdmin values(14);
INSERT INTO GymAdmin values(15);


INSERT INTO Gym values('Steve Nash', 'Cambie St', 'Vancouver', 11);
INSERT INTO Gym values('Steve Nash', 'Arbutus', 'Vancouver', 11);
INSERT INTO Gym values('Crossfit Untamed', 'Broadway', 'Vancouver', 12);
INSERT INTO Gym values('Golds Gym', 'UBC Campus', 'UBC', 13);
INSERT INTO Gym values('Birdcoop', 'UBC Campus', 'UBC', 14);


INSERT INTO GymClass values(1, 'Spin', 15.00, 11, 6, 'Steve Nash', 'Cambie St', '2016-11-13', '1330', '1430');
INSERT INTO GymClass values(2, 'Yoga', 35.00, 12, 7, 'Steve Nash', 'Arbutus', '2016-10-25', '1430', '1530');
INSERT INTO GymClass values(3, 'Power Lifting', 20.50, 11, 8, 'Birdcoop', 'UBC Campus', '2016-11-13', '1500', '1600');
INSERT INTO GymClass values(4, 'Spin', 15.00, 11, 9, 'Steve Nash', 'Cambie St', '2016-11-17', '1630', '1730');
INSERT INTO GymClass values(5, 'Squats', 50.00, 15, 10, 'Crossfit Untamed', 'Broadway', '2016-10-31', '2030', '2130');


INSERT INTO Routine values('Bicycling', 4, 5, 7, 6, 1);
INSERT INTO Routine values('Power Yoga', 8, 2, 3, 12, 2);
INSERT INTO Routine values('Heavy Lifting', 9, 5, 7, 6, 3);
INSERT INTO Routine values('Bicycling', 5, 5, 7, 9, 4);
INSERT INTO Routine values('Squats', 2, 5, 10, 15, 5);


INSERT INTO Attends values(1, 1);
INSERT INTO Attends values(2, 2);
INSERT INTO Attends values(3, 3);
INSERT INTO Attends values(4, 4);
INSERT INTO Attends values(5, 5);


INSERT INTO WorkOn values('Bicycling', 4, 4);
INSERT INTO WorkOn values('Bicycling', 5, 5);
INSERT INTO WorkOn values('Heavy Lifting', 9, 3);
INSERT INTO WorkOn values('Power Yoga', 8, 3);
INSERT INTO WorkOn values('Squats', 2, 1);


INSERT INTO Exercise values('Vertical Row', 'Back', 'Lats get swole', 'Heavy Lifting', 9, 13);
INSERT INTO Exercise values('Downward Dog', 'Hamstrings', 'Stretches legs', 'Power Yoga', 8, 9);
INSERT INTO Exercise values('Elliptical', 'Legs', 'Cardio', 'Bicycling', 4, 6);
INSERT INTO Exercise values('Barbell Squat', 'Glutes', 'Gives glutes more resistance', 'Squats', 2, 3);
INSERT INTO Exercise values('Shoulder Press', 'Shoulders', 'Shoulders get swole', 'Heavy Lifting', 9, 13);