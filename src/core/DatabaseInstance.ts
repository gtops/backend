import { Sequelize, SequelizeOptions } from "sequelize-typescript";
import { IDescriptionENV } from "../config/IDescriptionENV";
import { models } from "../models/models";
import { Tool } from "../tools/Tool";

class DatabaseContext {
	private _service: Sequelize;

	public async configure(): Promise<Sequelize> {
		const env: IDescriptionENV = Tool.getEnvironment();
		const options: SequelizeOptions = {
			host: "localhost",
			database: "postgres",
			username: "postgres",
			password: "qwerty123",
			port: 5432,
			dialect: "postgres",
			// sync: {force: true}
		};
		this._service = new Sequelize(options);
		this._service.addModels(models);
		await this._service.sync();
		return this._service;
	}

	public get service(): Sequelize {
		return this._service;
	}
}

export const databaseInstance = new DatabaseContext();
