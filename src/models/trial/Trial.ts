import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("trial"))
export class Trial extends Model<Trial> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public trial_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public name_of_trial: string;
}
