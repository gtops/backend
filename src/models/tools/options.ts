import * as _ from "lodash";
import { TableOptions } from "sequelize-typescript";

export function getOptions<T>(tableName: string, opt: TableOptions = {}): TableOptions {
	const hasTimestamp = _.get(opt, "timestamps", true);
	return {
		freezeTableName: true,
		underscored: true,
		timestamps: hasTimestamp,
		createdAt: "created_at",
		updatedAt: "updated_at",
		tableName,
	};
}
