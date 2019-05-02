import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("competition"))
export class Competition extends Model<Competition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public competition_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column
	public date_of_competition: Date;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public user_id: number;

	@Column(DataType.STRING(50))
	public name: string;
}
