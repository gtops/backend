import { models } from "@models/models";
import { Sequelize, SequelizeOptions } from "sequelize-typescript";

class DatabaseContext {
	private _service: Sequelize;

	public async configure(): Promise<Sequelize> {
		const options: SequelizeOptions = {
			host: "petrodim.beget.tech",
			database: "petrodim_test_db",
			username: "petrodim_test_db",
			password: "M2&pWHkR",
			port: 3306,
			dialect: "mysql"
		};
		this._service = new Sequelize(options);
		this._service.addModels(models);
		await this._service.sync({force: true});
		return this._service;
	}

	public get service(): Sequelize {
		return this._service;
	}
}

export const databaseInstance = new DatabaseContext();
