import {
	AllowNull,
	Column,
	DataType,
	HasMany,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";

import { getOptions } from "../tools/options";
import { User } from "../user/User";
import { WorkerOfUser } from "../workerOfUser/WorkerOfUser";
import { WorkerOfUserInCompetition } from "../WorkerOfUserInCompetition/WorkerOfUserInCompetition";

@Table(getOptions("role"))
export class Role extends Model<Role> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public role_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public name_of_role: string;

	@HasMany(() => User)
	public user: User[];

	@HasMany(() => WorkerOfUser)
	public workerOfUser: WorkerOfUser[];

	@HasMany(() => WorkerOfUserInCompetition)
	public workerOfUserInCompetition: WorkerOfUserInCompetition[];
}
