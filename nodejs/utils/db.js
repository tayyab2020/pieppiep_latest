'use strict';

const mysql = require('mysql');
const config = require('config');
const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '../../vendor/markury/src/.env') });

class Db {
	constructor() {
		this.connection = mysql.createPool({
			connectionLimit: 100,
			host: process.env.DB_HOST,
			user: process.env.DB_USERNAME,
			password: process.env.DB_PASSWORD,
			database: process.env.DB_DATABASE,
			debug: false
		});
	}
	query(sql, args) {
		return new Promise((resolve, reject) => {
			this.connection.query(sql, args, (err, rows) => {
				if (err)
					return reject(err);
				resolve(rows);
			});
		});
	}
	close() {
		return new Promise((resolve, reject) => {
			this.connection.end(err => {
				if (err)
					return reject(err);
				resolve();
			});
		});
	}
}
module.exports = new Db();
