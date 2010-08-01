create table screenshots (
  id int auto_increment not null,
  url varchar(256),
  description text,
  created timestamp default CURRENT_TIMESTAMP,
  approved boolean default false,
  
  primary key(id)
);

insert into screenshots values(null, "screenshots/screenshot.jpg", "Test image", null, null);

create table movies (
  id int auto_increment not null,
  url varchar(256),
  description text,
  created timestamp default CURRENT_TIMESTAMP,
  approved boolean default false,
  
  primary key(id)
);



create table news (
  id int auto_increment not null,
  title varchar(256),
  shortDescription varchar(256), 
  extendedDescription text,
  detailedDescription text,
  active int default 1,
  news_type_id int,
  created timestamp default CURRENT_TIMESTAMP,
  updateCount int default 0,
  primary key(id)
);

create table news_type (
  id int auto_increment not null,
  title varchar(255),
  image_url varchar(255),
  active int default 1,

  primary key(id)
);

insert into news_type(title, image_url) values ('Administrative', '/images/events/eventAdmin.png');
insert into news_type(title, image_url) values ('Gift', '/images/events/eventGift.png');
insert into news_type(title, image_url) values ('Meeting', '/images/events/eventMeeting.png');
insert into news_type(title, image_url) values ('Quiz', '/images/events/eventQuiz.png');
insert into news_type(title, image_url) values ('Raid', '/images/events/eventRaid.png');
insert into news_type(title, image_url) values ('Release', '/images/events/eventRelease.png');
insert into news_type(title, image_url) values ('Other', '/images/events/eventOther.png');

insert into news (title, shortDescription, extendedDescription, detailedDescription, news_type_id ) values ( "Edit this with your news title", "     ", 
"An html formatted description can go here.<p>","More description to be hidden behind a link can go here", 1);

create table news_images (
  id int auto_increment not null,
  news_id int not null,
  url varchar(256),
  description text,
  created timestamp default CURRENT_TIMESTAMP,
  
  primary key(id, news_id)
);

create table remind_password (
  username varchar(32) not null,
  confirmhash varchar(32) not null,
  
  requested timestamp default CURRENT_TIMESTAMP,
  
  primary key(username, confirmhash)
);
