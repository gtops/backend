import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("command"))
export class Command extends Model<Command> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public command_id: number;

	@AllowNull(true)
	@Unique(true)
	public competition_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public name_of_command: string;
}
