import { AllowNull, Column, DataType, HasMany, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { Participant } from "../participant/Participant";
import { getOptions } from "../tools/options";

@Table(getOptions("command"))
export class Command extends Model<Command> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public command_id: number;

	@AllowNull(true)
	@Unique(true)
	@Column(DataType.INTEGER)
	public competition_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public name_of_command: string;

	@HasMany(() => Participant)
	public participant: Participant[];
}
