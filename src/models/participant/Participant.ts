import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("participant"))
export class Participant extends Model<Participant> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public participant_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public name: string;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public surname: string;

	@AllowNull(true)
	@Unique(true)
	public command_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public uid: string;

	@AllowNull(false)
	@Unique(false)
	@Column
	public data_of_birth: Date;

	@Column(DataType.INTEGER)
	public gender_id: number;
}
