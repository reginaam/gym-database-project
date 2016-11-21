DROP TABLE Attends;
DROP TABLE FollowSchedule;
DROP TABLE ClassSchedule;
DROP TABLE WorkOn;
DROP TABLE Exercise;
DROP TABLE Routine;
DROP TABLE GymClass;
DROP TABLE Gym;
DROP TABLE Athlete;
DROP TABLE Trainer;
DROP TABLE GymAdmin;
DROP TABLE GymUser;


CREATE TABLE GymUser(
    membership_id int not null,
    email varchar(40) null,
    name varchar(40) null,
    phone_number char(12) null,
    primary key (membership_id));

CREATE TABLE Athlete (
    membership_id int not null,
    primary key (membership_id),
    foreign key (membership_id) references GymUser ON DELETE CASCADE);

CREATE TABLE Trainer (
    membership_id int not null,
    primary key (membership_id),
    foreign key (membership_id) references GymUser ON DELETE CASCADE);

CREATE TABLE GymAdmin (
    membership_id int not null,
    primary key (membership_id),
    foreign key (membership_id) references GymUser ON DELETE CASCADE);
    
CREATE TABLE Gym (
    gym_name varchar(80) not null,
    gym_location varchar(80) not null,
    city varchar(40) null,
    membership_id int null,
    primary key (gym_name, gym_location),
    foreign key (membership_id) references GymAdmin);

CREATE TABLE GymClass (
    class_id int not null,
    name varchar(80) null,
    cost decimal(6,2) null,
    admin_membership_id int null,
    trainer_membership_id int not null,
    gym_name varchar(80) not null,
    gym_location varchar(80) not null,
    primary key (class_id),
    foreign key (admin_membership_id) references GymAdmin,
    foreign key (trainer_membership_id) references Trainer,
    foreign key (gym_name, gym_location) references Gym);
    
CREATE TABLE Routine (
    routine_name varchar(80) not null,
    intensity int not null,
    sets int null,
    reps int null,
    membership_id int null,
    class_id int null,
    primary key (routine_name, intensity),
    foreign key (membership_id) references GymUser,
    foreign key (class_id) references GymClass,
    constraint int_pos check(intensity > 0),
    constraint int_max check(intensity < 11));

CREATE TABLE Attends (
    class_id int not null,
    membership_id int not null,
    primary key (class_id, membership_id),
    foreign key (class_id) references GymClass,
    foreign key (membership_id) references Athlete);
    
CREATE TABLE ClassSchedule (
    class_date date not null,
    start_time char(4) not null,
    end_time char(4) not null,
    primary key (class_date, start_time, end_time));

CREATE TABLE FollowSchedule (
    class_id int not null,
    class_date date not null,
    start_time char(4) not null,
    end_time char(4) not null,
    primary key (class_id, class_date, start_time, end_time),
    foreign key (class_id) references GymClass,
    foreign key (class_date, start_time, end_time) references ClassSchedule);

CREATE TABLE WorkOn (
    routine_name varchar(80) not null,
    intensity int not null,
    membership_id int not null,
    primary key (routine_name, intensity, membership_id),
    foreign key (routine_name, intensity) references Routine,
    foreign key (membership_id) references Athlete);

CREATE TABLE Exercise (
    exercise_name varchar(80) not null,
    body_part varchar(20) not null,
    benefit varchar(500) null,
    routine_name varchar(80) null,
    intensity int null,
    membership_id int null,
    primary key (exercise_name, body_part),
    foreign key (routine_name, intensity) references Routine,
    foreign key (membership_id) references GymUser);